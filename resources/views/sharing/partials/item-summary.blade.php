<div class="item-summary">
    @include('sharing.partials.item-art', ['catalog' => $item->catalogItem, 'artClass' => 'art-small'])
    <div><span class="eyebrow">{{ $item->catalogItem->category }}</span><h2>{{ $item->catalogItem->title }}</h2><p>Rp {{ number_format($item->catalogItem->daily_price, 0, ',', '.') }} / day <span class="muted">· {{ $item->owner->name }}</span></p></div>
    <span class="badge {{ $item->catalogItem->status === 'available' ? 'available' : 'unavailable' }}">{{ ucfirst($item->catalogItem->status) }}</span>
</div>
