<?php

namespace App\Http\Controllers\Sharing;

use App\Http\Controllers\Controller;
use App\Models\Sharing\SharingRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SharingTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $actor = $request->attributes->get('sharing_actor');
        $data = $request->validate(['tab' => ['nullable', 'in:pending,active,completed']]);
        $tab = $data['tab'] ?? 'pending';
        $history = SharingRequest::query()->where(function (Builder $query) use ($actor): void {
            $query->where('borrower_id', $actor->id)->orWhere('owner_id', $actor->id);
        });
        $counts = [
            'pending' => (clone $history)->where('status', 'pending')->count(),
            'active' => (clone $history)->whereHas('transaction', fn (Builder $query): Builder => $query->where('status', 'active'))->count(),
            'completed' => (clone $history)->whereHas('transaction', fn (Builder $query): Builder => $query->where('status', 'completed'))->count(),
        ];
        $entries = (clone $history)->with(['item.catalogItem', 'borrower', 'owner', 'transaction']);
        if ($tab === 'pending') {
            $entries->where('status', 'pending');
        } else {
            $entries->whereHas('transaction', fn (Builder $query): Builder => $query->where('status', $tab));
        }
        $entries = $entries->latest('updated_at')->orderByDesc('id')->paginate(10)->withQueryString();
        $decisions = (clone $history)->with(['item.catalogItem', 'borrower', 'owner'])
            ->whereIn('status', ['rejected', 'cancelled'])->latest('updated_at')->orderByDesc('id')
            ->paginate(5, ['*'], 'decisions_page')->withQueryString();

        return view('sharing.transactions.index', compact('tab', 'entries', 'counts', 'decisions'));
    }
}
