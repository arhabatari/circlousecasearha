@extends('sharing.layout')
@section('title', 'Explore shared items')
@section('content')
<div class="page-heading"><div><span class="eyebrow">YOUR CAMPUS, CONNECTED</span><h1>Find what you need.</h1><p>Borrow something useful. Share a little more.</p></div><a class="button secondary" href="{{ route('sharing.transactions.index') }}">My Transactions →</a></div>
<div class="catalog-grid">
    @forelse($items as $item)
        <article class="catalog-card">@include('sharing.partials.item-art', ['catalog' => $item->catalogItem])<div class="catalog-body"><div class="row"><span class="eyebrow">{{ $item->catalogItem->category }}</span><span class="badge {{ $item->catalogItem->status === 'available' ? 'available' : 'unavailable' }}">{{ ucfirst($item->catalogItem->status) }}</span></div><h2>{{ $item->catalogItem->title }}</h2><p class="muted">Shared by {{ $item->owner->name }} @if($actor->id === $item->owner_id) · Your listing @endif</p><div class="catalog-bottom"><strong class="price">Rp {{ number_format($item->catalogItem->daily_price, 0, ',', '.') }}<small> / day</small></strong><a href="{{ route('sharing.items.show', $item) }}" class="text-link" aria-label="View {{ $item->catalogItem->title }}">View item →</a></div></div></article>
    @empty
        <div class="card empty-state"><h2>No items yet</h2><p>Demo listings will appear here after the demo seeder has run.</p></div>
    @endforelse
</div>
@endsection
