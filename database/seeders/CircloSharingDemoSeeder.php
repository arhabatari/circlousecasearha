<?php

namespace Database\Seeders;

use App\Models\Sharing\CatalogItem;
use App\Models\Sharing\DemoActor;
use App\Models\Sharing\SharingItem;
use App\Models\Sharing\SharingRequest;
use App\Models\Sharing\SharingTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CircloSharingDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('CircloSharingDemoSeeder is for local/testing environments only.');
        }

        DB::transaction(function (): void {
            $actors = [];
            $students = [
                'dyandra' => 'Dyandra Putri', 'budi' => 'Budi Santoso', 'dewi' => 'Dewi Lestari',
                'rizky' => 'Rizky Aditya', 'sari' => 'Sari Meilani', 'andi' => 'Andi Pratama', 'tono' => 'Tono Wijaya',
            ];
            $index = 0;
            foreach ($students as $slug => $name) {
                $actors[$slug] = DemoActor::firstOrCreate(['slug' => $slug], [
                    'name' => $name, 'email' => $slug.'@demo.circlo.test',
                    'student_number' => (string) (5026231100 + ++$index),
                ]);
            }

            $samples = [
                ['Macbook Air M1', 'Electronics', 'Budi S.', 50000, 'available', 'budi'],
                ['Textbook Calculus', 'Books', 'Dewi L.', 15000, 'available', 'dewi'],
                ['Canon Camera', 'Electronics', 'Rizky A.', 75000, 'booked', 'rizky'],
                ['Camping Tent', 'Equipment', 'Sari M.', 40000, 'available', 'sari'],
                ['Jaket Hoodie', 'Fashion', 'Andi P.', 20000, 'available', 'andi'],
                ['Road Bike', 'Sports', 'Tono W.', 60000, 'booked', 'tono'],
            ];
            $items = [];
            foreach ($samples as [$title, $category, $label, $price, $status, $slug]) {
                $catalog = CatalogItem::firstOrCreate(['title' => $title, 'owner' => $label], [
                    'category' => $category, 'daily_price' => $price, 'status' => $status,
                ]);
                $items[$title] = SharingItem::firstOrCreate(['demo_item_id' => $catalog->id], [
                    'owner_id' => $actors[$slug]->id, 'pickup_location' => 'ITS Library entrance',
                    'description' => 'A campus sharing demo item. Please agree on pickup and return arrangements with the owner.',
                ]);
            }

            $today = today(config('circlo-sharing.timezone'))->toImmutable();
            $history = [
                ['pending-laptop', 'Macbook Air M1', 'dyandra', 4, 6, 'pending', null, 'I need a laptop for our group presentation. Thank you!'],
                ['pending-book', 'Textbook Calculus', 'rizky', 9, 11, 'pending', null, 'May I borrow this for exam preparation?'],
                ['active-tent', 'Camping Tent', 'dyandra', 1, 3, 'accepted', 'active', 'For our campus outdoor club trip.'],
                ['completed-book', 'Textbook Calculus', 'dyandra', -8, -6, 'accepted', 'completed', 'Thank you for sharing your textbook.'],
                ['rejected-laptop', 'Macbook Air M1', 'dyandra', -12, -10, 'rejected', null, 'May I rent this for a weekend?'],
            ];

            foreach ($history as [$key, $title, $slug, $startOffset, $endOffset, $status, $transactionStatus, $message]) {
                $item = $items[$title];
                $price = $item->catalogItem->daily_price;
                $sharingRequest = SharingRequest::firstOrCreate(['demo_key' => $key], [
                    'item_id' => $item->id, 'borrower_id' => $actors[$slug]->id, 'owner_id' => $item->owner_id,
                    'starts_on' => $today->addDays($startOffset), 'ends_on' => $today->addDays($endOffset),
                    'message' => $message, 'daily_price' => $price,
                    'total_price' => ($endOffset - $startOffset + 1) * $price, 'status' => $status,
                    'decided_at' => $status === 'pending' ? null : now(),
                    'rejection_reason' => $status === 'rejected' ? 'I needed the laptop for my own coursework on those dates.' : null,
                ]);

                if ($sharingRequest->wasRecentlyCreated && $transactionStatus !== null) {
                    SharingTransaction::create([
                        'request_id' => $sharingRequest->id, 'status' => $transactionStatus,
                        'completed_at' => $transactionStatus === 'completed' ? $today->subDays(6) : null,
                    ]);
                }
            }
        });
    }
}
