<?php

namespace App\Services;

use App\Enums\SharingRequestStatus;
use App\Enums\SharingTransactionStatus;
use App\Models\Sharing\DemoActor;
use App\Models\Sharing\SharingItem;
use App\Models\Sharing\SharingRequest;
use App\Models\Sharing\SharingTransaction;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SharingRequestService
{
    /** @param array{starts_on: string, ends_on: string, message?: ?string} $data */
    public function submit(SharingItem $item, DemoActor $borrower, array $data): SharingRequest
    {
        return DB::transaction(function () use ($item, $borrower, $data): SharingRequest {
            $lockedItem = SharingItem::with('catalogItem')->lockForUpdate()->findOrFail($item->id);

            if ($lockedItem->owner_id === $borrower->id) {
                throw ValidationException::withMessages(['item' => 'You cannot request your own item.']);
            }

            $pending = SharingRequest::where('item_id', $item->id)->where('borrower_id', $borrower->id)
                ->where('status', SharingRequestStatus::Pending->value)->first();

            if ($pending) {
                return $pending;
            }

            $start = CarbonImmutable::parse($data['starts_on'])->startOfDay();
            $end = CarbonImmutable::parse($data['ends_on'])->startOfDay();
            $this->ensureAvailable($lockedItem, $start, $end);
            $days = (int) $start->diffInDays($end) + 1;

            return SharingRequest::create([
                'item_id' => $lockedItem->id, 'borrower_id' => $borrower->id, 'owner_id' => $lockedItem->owner_id,
                'starts_on' => $start, 'ends_on' => $end, 'message' => $data['message'] ?? null,
                'status' => SharingRequestStatus::Pending,
                'daily_price' => $lockedItem->catalogItem->daily_price,
                'total_price' => $days * $lockedItem->catalogItem->daily_price,
            ]);
        }, attempts: 3);
    }

    public function accept(SharingRequest $sharingRequest, DemoActor $owner): SharingTransaction
    {
        return DB::transaction(function () use ($sharingRequest, $owner): SharingTransaction {
            $item = SharingItem::with('catalogItem')->lockForUpdate()->findOrFail($sharingRequest->item_id);
            $pending = SharingRequest::lockForUpdate()->findOrFail($sharingRequest->id);
            abort_unless($pending->owner_id === $owner->id && $item->owner_id === $owner->id, 403);
            $this->ensurePending($pending);

            if ($pending->starts_on->toDateString() < CarbonImmutable::today(config('circlo-sharing.timezone'))->toDateString()) {
                throw ValidationException::withMessages(['request' => 'This request has expired. Reject it and ask the student to choose new dates.']);
            }

            $this->ensureAvailable($item, $pending->starts_on, $pending->ends_on);
            $pending->update(['status' => SharingRequestStatus::Accepted, 'decided_at' => now()]);

            return SharingTransaction::create(['request_id' => $pending->id, 'status' => SharingTransactionStatus::Active]);
        }, attempts: 3);
    }

    public function reject(SharingRequest $sharingRequest, DemoActor $owner, ?string $reason): void
    {
        DB::transaction(function () use ($sharingRequest, $owner, $reason): void {
            $item = SharingItem::lockForUpdate()->findOrFail($sharingRequest->item_id);
            $pending = SharingRequest::lockForUpdate()->findOrFail($sharingRequest->id);
            abort_unless($pending->owner_id === $owner->id && $item->owner_id === $owner->id, 403);
            $this->ensurePending($pending);
            $pending->update([
                'status' => SharingRequestStatus::Rejected,
                'rejection_reason' => $reason, 'decided_at' => now(),
            ]);
        }, attempts: 3);
    }

    protected function ensurePending(SharingRequest $sharingRequest): void
    {
        if ($sharingRequest->status !== SharingRequestStatus::Pending) {
            throw ValidationException::withMessages(['request' => 'This request has already been decided and cannot be changed.']);
        }
    }

    protected function ensureAvailable(SharingItem $item, CarbonImmutable $start, CarbonImmutable $end): void
    {
        if ($item->catalogItem->status !== 'available') {
            throw ValidationException::withMessages(['item' => 'This item is currently unavailable. Please choose another item.']);
        }

        $overlaps = SharingTransaction::where('status', SharingTransactionStatus::Active->value)
            ->whereHas('sharingRequest', function (Builder $query) use ($item, $start, $end): void {
                $query->where('item_id', $item->id)->whereDate('starts_on', '<=', $end->toDateString())
                    ->whereDate('ends_on', '>=', $start->toDateString());
            })->exists();

        if ($overlaps) {
            throw ValidationException::withMessages(['starts_on' => 'This item is not available for the chosen period. Please choose different dates.']);
        }
    }
}
