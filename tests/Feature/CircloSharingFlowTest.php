<?php

namespace Tests\Feature;

use App\Enums\SharingRequestStatus;
use App\Models\Sharing\CatalogItem;
use App\Models\Sharing\DemoActor;
use App\Models\Sharing\SharingItem;
use App\Models\Sharing\SharingRequest;
use App\Models\Sharing\SharingTransaction;
use Carbon\CarbonImmutable;
use Database\Seeders\CircloSharingDemoSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CircloSharingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite', config('database.default'), 'Run only with phpunit.circlo.xml.');
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $this->travelTo(CarbonImmutable::create(2026, 11, 16, 12, 0, 0, 'Asia/Jakarta'));
        $this->withoutVite();
        $this->seed(CircloSharingDemoSeeder::class);
    }

    protected function actor(string $slug): DemoActor
    {
        return DemoActor::where('slug', $slug)->firstOrFail();
    }

    protected function textbook(): SharingItem
    {
        return SharingItem::whereHas('catalogItem', fn (Builder $query): Builder => $query->where('title', 'Textbook Calculus'))->firstOrFail();
    }

    protected function period(): array
    {
        return ['starts_on' => '2026-11-30', 'ends_on' => '2026-12-02', 'message' => 'For my exam preparation.'];
    }

    protected function pending(string $borrower = 'dyandra'): SharingRequest
    {
        $item = $this->textbook();

        return SharingRequest::create(array_merge($this->period(), [
            'item_id' => $item->id, 'borrower_id' => $this->actor($borrower)->id, 'owner_id' => $item->owner_id,
            'daily_price' => 15000, 'total_price' => 45000, 'status' => 'pending',
        ]));
    }

    protected function asStudent(string $slug): static
    {
        return $this->withSession(['circlo_sharing_actor_id' => $this->actor($slug)->id]);
    }

    public function test_original_home_routes_and_legacy_catalog_are_preserved(): void
    {
        $this->assertSame('/', route('landing', [], false));
        $this->assertSame('/home', route('home', [], false));
        $this->get('/')->assertOk()->assertSee('CIRCLO');
        $this->get('/home')->assertOk()->assertSee('Dyandra');
        $this->assertSame(6, CatalogItem::count());
        $this->assertSame(4, CatalogItem::where('status', 'available')->count());
    }

    public function test_demo_actor_selection_is_explicit_and_session_based(): void
    {
        $this->get('/sharing/transactions')->assertRedirect(route('sharing.demo'));
        $this->get('/sharing/demo')->assertOk()->assertSee('not a login');
        $this->post('/sharing/demo/actor', ['actor_id' => $this->actor('dyandra')->id])
            ->assertRedirect(route('sharing.transactions.index'))->assertSessionHas('circlo_sharing_actor_id', $this->actor('dyandra')->id);
        $this->post('/sharing/demo/end')->assertRedirect(route('sharing.demo'))->assertSessionMissing('circlo_sharing_actor_id');
    }

    public function test_invalid_demo_actor_is_rejected(): void
    {
        $this->post('/sharing/demo/actor', ['actor_id' => 999999])->assertSessionHasErrors('actor_id');
        $this->withSession(['circlo_sharing_actor_id' => 999999])->get('/sharing/transactions')->assertRedirect(route('sharing.demo'));
    }

    public function test_demo_is_disabled_outside_local_or_testing_even_when_flag_is_true(): void
    {
        $this->app->instance('env', 'production');
        $this->get('/sharing/demo')->assertNotFound();
        $this->asStudent('dyandra')->withSession(['_token' => 'test-token'])->post('/sharing/items/'.$this->textbook()->id.'/requests', array_merge($this->period(), ['_token' => 'test-token']))->assertNotFound();
    }

    public function test_demo_can_be_disabled_locally(): void
    {
        config(['circlo-sharing.demo_enabled' => false]);
        $this->get('/sharing/demo')->assertNotFound();
    }

    public function test_catalog_item_form_and_details_render(): void
    {
        $item = $this->textbook();
        $this->asStudent('dyandra')->get('/sharing/items')->assertOk()->assertSee('Textbook Calculus');
        $this->get('/sharing/items/'.$item->id)->assertOk()->assertSee('Borrow / Rent');
        $this->get('/sharing/items/'.$item->id.'/request')->assertOk()->assertSee('Submit Request')->assertSee('name="starts_on"', false);
    }

    public function test_submit_stores_pending_request_and_server_calculated_quote(): void
    {
        $item = $this->textbook();
        $this->asStudent('dyandra')->post('/sharing/items/'.$item->id.'/requests', array_merge($this->period(), [
            'borrower_id' => $this->actor('budi')->id, 'owner_id' => $this->actor('andi')->id,
            'status' => 'accepted', 'total_price' => 1,
        ]))->assertRedirect(route('sharing.transactions.index', ['tab' => 'pending']))->assertSessionHas('success');
        $this->assertDatabaseHas('circlo_sharing_requests', [
            'item_id' => $item->id, 'borrower_id' => $this->actor('dyandra')->id, 'owner_id' => $this->actor('dewi')->id,
            'status' => 'pending', 'daily_price' => 15000, 'total_price' => 45000,
        ]);
        $this->get('/sharing/transactions?tab=pending')->assertOk()->assertSee('Textbook Calculus');
    }

    public function test_same_day_period_costs_one_day(): void
    {
        $data = array_merge($this->period(), ['ends_on' => '2026-11-30']);
        $this->asStudent('dyandra')->post('/sharing/items/'.$this->textbook()->id.'/requests', $data)->assertSessionHasNoErrors();
        $stored = SharingRequest::where('borrower_id', $this->actor('dyandra')->id)->where('status', 'pending')->where('item_id', $this->textbook()->id)->firstOrFail();
        $this->assertSame('2026-11-30', $stored->starts_on->toDateString());
        $this->assertSame(15000, $stored->total_price);
    }

    public static function invalidPeriods(): array
    {
        return [
            'empty dates' => [[], ['starts_on', 'ends_on']],
            'reversed' => [['starts_on' => '2026-11-30', 'ends_on' => '2026-11-29'], ['ends_on']],
            'past' => [['starts_on' => '2026-11-15', 'ends_on' => '2026-11-16'], ['starts_on']],
            'impossible date' => [['starts_on' => '2026-11-31', 'ends_on' => '2026-12-02'], ['starts_on']],
            'wrong format' => [['starts_on' => '30/11/2026', 'ends_on' => '2026-12-02'], ['starts_on']],
        ];
    }

    #[DataProvider('invalidPeriods')]
    public function test_invalid_dates_do_not_insert(array $data, array $fields): void
    {
        $count = SharingRequest::count();
        $this->asStudent('dyandra')->from('/sharing/items/'.$this->textbook()->id.'/request')
            ->post('/sharing/items/'.$this->textbook()->id.'/requests', $data)->assertSessionHasErrors($fields);
        $this->assertSame($count, SharingRequest::count());
    }

    public function test_own_item_and_unavailable_item_cannot_be_requested(): void
    {
        $this->asStudent('dewi')->post('/sharing/items/'.$this->textbook()->id.'/requests', $this->period())->assertSessionHasErrors('item');
        $unavailable = SharingItem::whereHas('catalogItem', fn (Builder $query): Builder => $query->where('status', 'booked'))->firstOrFail();
        $this->asStudent('dyandra')->post('/sharing/items/'.$unavailable->id.'/requests', $this->period())->assertSessionHasErrors('item');
    }

    public function test_duplicate_pending_request_redirects_without_another_insert(): void
    {
        $this->pending();
        $count = SharingRequest::count();
        $this->asStudent('dyandra')->post('/sharing/items/'.$this->textbook()->id.'/requests', $this->period())
            ->assertRedirect(route('sharing.transactions.index', ['tab' => 'pending']))->assertSessionHas('notice');
        $this->assertSame($count, SharingRequest::count());
    }

    public function test_pending_uniqueness_is_also_enforced_by_database(): void
    {
        $pending = $this->pending();
        $this->expectException(QueryException::class);
        $pending->replicate()->save();
    }

    public function test_owner_can_accept_and_both_students_see_one_active_transaction(): void
    {
        $pending = $this->pending();
        $this->asStudent('dewi')->get('/sharing/incoming')->assertOk()->assertSee('Dyandra Putri');
        $this->get('/sharing/requests/'.$pending->id)->assertOk()->assertSee('Accept Request');
        $this->post('/sharing/requests/'.$pending->id.'/accept')->assertRedirect(route('sharing.transactions.index', ['tab' => 'active']));
        $this->assertSame(SharingRequestStatus::Accepted, $pending->fresh()->status);
        $this->assertSame(1, SharingTransaction::where('request_id', $pending->id)->where('status', 'active')->count());
        $this->get('/sharing/transactions?tab=active')->assertOk()->assertSee('Textbook Calculus');
        $this->asStudent('dyandra')->get('/sharing/transactions?tab=active')->assertOk()->assertSee('Textbook Calculus');
    }

    public function test_only_owner_can_decide_and_only_participants_can_read_detail(): void
    {
        $pending = $this->pending();
        foreach (['dyandra', 'andi'] as $slug) {
            $this->asStudent($slug)->post('/sharing/requests/'.$pending->id.'/accept')->assertForbidden();
            $this->post('/sharing/requests/'.$pending->id.'/reject')->assertForbidden();
        }
        $this->asStudent('andi')->get('/sharing/requests/'.$pending->id)->assertForbidden();
        $this->asStudent('dyandra')->get('/sharing/requests/'.$pending->id)->assertOk()->assertDontSee('Accept Request');
        $this->assertSame(SharingRequestStatus::Pending, $pending->fresh()->status);
    }

    public function test_reject_stores_optional_reason_without_creating_transaction(): void
    {
        $pending = $this->pending();
        $this->asStudent('dewi')->post('/sharing/requests/'.$pending->id.'/reject', ['rejection_reason' => 'I need it for my exam.'])
            ->assertRedirect(route('sharing.requests.incoming'));
        $this->assertSame(SharingRequestStatus::Rejected, $pending->fresh()->status);
        $this->assertDatabaseMissing('circlo_sharing_transactions', ['request_id' => $pending->id]);
        $this->asStudent('dyandra')->get('/sharing/transactions')->assertOk()->assertSee('I need it for my exam.')->assertSee('Rejected');
    }

    public function test_rejection_without_a_reason_is_allowed(): void
    {
        $pending = $this->pending();
        $this->asStudent('dewi')->post('/sharing/requests/'.$pending->id.'/reject')->assertSessionHasNoErrors();
        $this->assertNull($pending->fresh()->rejection_reason);
    }

    public function test_repeated_accept_or_reject_cannot_rewrite_a_decision(): void
    {
        $pending = $this->pending();
        $this->asStudent('dewi')->post('/sharing/requests/'.$pending->id.'/accept')->assertSessionHasNoErrors();
        $this->post('/sharing/requests/'.$pending->id.'/accept')->assertSessionHasErrors('request');
        $this->post('/sharing/requests/'.$pending->id.'/reject')->assertSessionHasErrors('request');
        $this->assertSame(1, SharingTransaction::where('request_id', $pending->id)->count());
        $this->assertSame(SharingRequestStatus::Accepted, $pending->fresh()->status);
    }

    public function test_overlapping_pending_requests_can_exist_but_only_one_can_be_accepted(): void
    {
        $first = $this->pending();
        $second = $this->pending('budi');
        $this->asStudent('dewi')->post('/sharing/requests/'.$first->id.'/accept')->assertSessionHasNoErrors();
        $this->post('/sharing/requests/'.$second->id.'/accept')->assertSessionHasErrors('starts_on');
        $this->assertSame(SharingRequestStatus::Pending, $second->fresh()->status);
        $this->assertDatabaseMissing('circlo_sharing_transactions', ['request_id' => $second->id]);
    }

    public function test_reservations_block_inclusive_boundary_dates_but_allow_other_periods(): void
    {
        $pending = $this->pending();
        $this->asStudent('dewi')->post('/sharing/requests/'.$pending->id.'/accept');
        $this->asStudent('budi')->post('/sharing/items/'.$this->textbook()->id.'/requests', [
            'starts_on' => '2026-12-02', 'ends_on' => '2026-12-03',
        ])->assertSessionHasErrors('starts_on');
        $this->post('/sharing/items/'.$this->textbook()->id.'/requests', [
            'starts_on' => '2026-12-03', 'ends_on' => '2026-12-04',
        ])->assertSessionHasNoErrors();
    }

    public function test_accept_rechecks_legacy_availability_and_expiration(): void
    {
        $pending = $this->pending();
        $pending->item->catalogItem->update(['status' => 'booked']);
        $this->asStudent('dewi')->post('/sharing/requests/'.$pending->id.'/accept')->assertSessionHasErrors('item');
        $pending->item->catalogItem->update(['status' => 'available']);
        $pending->update(['starts_on' => '2026-11-14', 'ends_on' => '2026-11-15']);
        $this->post('/sharing/requests/'.$pending->id.'/accept')->assertSessionHasErrors('request');
        $this->assertDatabaseMissing('circlo_sharing_transactions', ['request_id' => $pending->id]);
    }

    public function test_tabs_are_filtered_and_other_students_history_is_private(): void
    {
        $this->asStudent('dyandra')->get('/sharing/transactions?tab=pending')->assertOk()->assertSee('Macbook Air M1')->assertDontSee('Camping Tent')->assertDontSee('@include', false);
        $this->get('/sharing/transactions?tab=active')->assertOk()->assertSee('Camping Tent');
        $this->get('/sharing/transactions?tab=completed')->assertOk()->assertSee('Textbook Calculus');
        $this->asStudent('andi')->get('/sharing/transactions')->assertOk()->assertSee('No pending requests yet')->assertDontSee('Macbook Air M1');
        $this->get('/sharing/incoming')->assertOk()->assertSee('No incoming requests yet');
    }

    public function test_message_is_escaped_and_leaving_detail_makes_no_decision(): void
    {
        $pending = $this->pending();
        $pending->update(['message' => '<script>alert("x")</script>']);
        $this->asStudent('dewi')->get('/sharing/requests/'.$pending->id)->assertOk()
            ->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert', false);
        $this->get('/sharing/incoming')->assertOk();
        $this->assertSame(SharingRequestStatus::Pending, $pending->fresh()->status);
    }

    public function test_seeder_is_repeatable_and_preserves_decisions_and_legacy_records(): void
    {
        $before = DB::table('circlo_demo_items')->orderBy('id')->get()->toJson();
        $pending = SharingRequest::where('demo_key', 'pending-laptop')->firstOrFail();
        $this->asStudent('budi')->post('/sharing/requests/'.$pending->id.'/accept')->assertSessionHasNoErrors();
        $counts = [DemoActor::count(), SharingItem::count(), SharingRequest::count(), SharingTransaction::count()];
        $this->seed(CircloSharingDemoSeeder::class);
        $this->assertSame($before, DB::table('circlo_demo_items')->orderBy('id')->get()->toJson());
        $this->assertSame($counts, [DemoActor::count(), SharingItem::count(), SharingRequest::count(), SharingTransaction::count()]);
        $this->assertSame(SharingRequestStatus::Accepted, $pending->fresh()->status);
    }

    public function test_oversized_notes_and_reasons_are_rejected(): void
    {
        $this->asStudent('dyandra')->post('/sharing/items/'.$this->textbook()->id.'/requests', array_merge($this->period(), ['message' => str_repeat('x', 2001)]))->assertSessionHasErrors('message');
        $pending = $this->pending();
        $this->asStudent('dewi')->post('/sharing/requests/'.$pending->id.'/reject', ['rejection_reason' => str_repeat('x', 1001)])->assertSessionHasErrors('rejection_reason');
        $this->assertSame(SharingRequestStatus::Pending, $pending->fresh()->status);
    }
}
