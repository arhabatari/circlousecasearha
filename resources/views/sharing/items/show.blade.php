@extends('sharing.layout')
@section('title', $item->catalogItem->title)
@section('content')
<a class="back-link" href="{{ route('sharing.items.index') }}">← Back to shared items</a>
<div class="page-heading"><div><span class="eyebrow">ITEM DETAILS</span><h1>{{ $item->catalogItem->title }}</h1><p>A useful item, a little closer to you.</p></div></div>
<div class="two-column"><section class="card">@include('sharing.partials.item-art', ['catalog' => $item->catalogItem])<h2>About this item</h2><p class="body-copy">{{ $item->description }}</p><dl class="detail-list"><div><dt>Owner</dt><dd>{{ $item->owner->name }}</dd></div><div><dt>Pickup point</dt><dd>{{ $item->pickup_location }}</dd></div><div><dt>Daily rate</dt><dd>Rp {{ number_format($item->catalogItem->daily_price, 0, ',', '.') }}</dd></div></dl></section>
<aside class="card"><h2>Plan your sharing period</h2><p class="muted">Start and end dates are included in the daily estimate.</p>
    @if($item->catalogItem->status !== 'available')<div class="alert notice">This item is currently booked or unavailable.</div>
    @elseif($item->owner_id === $actor->id)<div class="alert notice">This is your item. You cannot request your own listing.</div>
    @else<a class="button primary full" href="{{ route('sharing.requests.create', $item) }}">Borrow / Rent →</a>@endif
    <h3 class="section-label">Reserved periods</h3><p class="small muted">Choose dates outside these confirmed reservations.</p>
    @forelse($reservations as $reservation)<div class="reserved-period">{{ $reservation->starts_on->format('d M Y') }} → {{ $reservation->ends_on->format('d M Y') }}</div>@empty<p class="small muted">No confirmed reservations yet.</p>@endforelse
</aside></div>
@endsection
