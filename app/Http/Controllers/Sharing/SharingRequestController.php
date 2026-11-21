<?php

namespace App\Http\Controllers\Sharing;

use App\Http\Controllers\Controller;
use App\Http\Requests\DecideSharingRequest;
use App\Http\Requests\StoreSharingRequest;
use App\Models\Sharing\SharingItem;
use App\Models\Sharing\SharingRequest;
use App\Services\SharingRequestService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SharingRequestController extends Controller
{
    public function create(SharingItem $item): View
    {
        $item->load(['catalogItem', 'owner']);

        return view('sharing.requests.create', compact('item'));
    }

    public function store(StoreSharingRequest $request, SharingItem $item, SharingRequestService $service): RedirectResponse
    {
        $sharingRequest = $service->submit($item, $request->attributes->get('sharing_actor'), $request->validated());

        return redirect()->route('sharing.transactions.index', ['tab' => 'pending'])
            ->with($sharingRequest->wasRecentlyCreated ? 'success' : 'notice', $sharingRequest->wasRecentlyCreated
                ? 'Your sharing request was submitted. The owner will review it soon.'
                : 'You already have a pending request for this item. You can find it below.');
    }

    public function incoming(Request $request): View
    {
        $requests = SharingRequest::with(['item.catalogItem', 'borrower'])
            ->where('owner_id', $request->attributes->get('sharing_actor')->id)
            ->where('status', 'pending')->latest('id')->paginate(12);

        return view('sharing.requests.incoming', compact('requests'));
    }

    public function show(Request $request, SharingRequest $sharingRequest): View
    {
        $actorId = $request->attributes->get('sharing_actor')->id;
        abort_unless(in_array($actorId, [$sharingRequest->borrower_id, $sharingRequest->owner_id], true), 403);
        $sharingRequest->load(['item.catalogItem', 'item.owner', 'borrower', 'owner', 'transaction']);

        return view('sharing.requests.show', compact('sharingRequest'));
    }

    public function accept(DecideSharingRequest $request, SharingRequest $sharingRequest, SharingRequestService $service): RedirectResponse
    {
        $service->accept($sharingRequest, $request->attributes->get('sharing_actor'));

        return redirect()->route('sharing.transactions.index', ['tab' => 'active'])->with('success', 'Request accepted. An active transaction is now visible to both students.');
    }

    public function reject(DecideSharingRequest $request, SharingRequest $sharingRequest, SharingRequestService $service): RedirectResponse
    {
        $service->reject($sharingRequest, $request->attributes->get('sharing_actor'), $request->validated('rejection_reason'));

        return redirect()->route('sharing.requests.incoming')->with('success', 'Request rejected. The student can see your decision in My Transactions.');
    }
}
