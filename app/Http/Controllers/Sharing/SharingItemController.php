<?php

namespace App\Http\Controllers\Sharing;

use App\Http\Controllers\Controller;
use App\Models\Sharing\SharingItem;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class SharingItemController extends Controller
{
    public function index(): View
    {
        return view('sharing.items.index', ['items' => SharingItem::with(['catalogItem', 'owner'])->orderBy('id')->get()]);
    }

    public function show(SharingItem $item): View
    {
        $item->load(['catalogItem', 'owner']);
        $reservations = $item->requests()->whereHas('transaction', fn (Builder $query): Builder => $query->where('status', 'active'))
            ->whereDate('ends_on', '>=', today())->orderBy('starts_on')->get(['starts_on', 'ends_on']);

        return view('sharing.items.show', compact('item', 'reservations'));
    }
}
