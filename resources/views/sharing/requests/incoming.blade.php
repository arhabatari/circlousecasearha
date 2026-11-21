@extends('sharing.layout')
@section('title', 'Incoming requests')
@section('content')
<div class="page-heading"><div><span class="eyebrow">MY ITEMS / ACTIVE REQUESTS</span><h1>Incoming requests</h1><p>Your items could make someone's day. Review their requests here.</p></div><span class="page-tag">{{ $requests->total() }} pending</span></div>
<section class="card"><div class="card-heading"><h2>Waiting for your decision</h2><span class="small muted">Only requests for your items</span></div>
    <div class="entry-list">@forelse($requests as $entry)<article class="entry">@include('sharing.partials.item-art', ['catalog' => $entry->item->catalogItem, 'artClass' => 'art-small'])<div class="entry-main"><span class="small muted">Request #{{ $entry->id }} · {{ $entry->borrower->name }}</span><h3>{{ $entry->item->catalogItem->title }}</h3><p>{{ $entry->starts_on->format('d M Y') }} → {{ $entry->ends_on->format('d M Y') }}</p></div><span class="badge pending">Pending</span><a class="button secondary" href="{{ route('sharing.requests.show', $entry) }}">Review request →</a></article>@empty<div class="empty-state"><div class="empty-symbol" aria-hidden="true">▤</div><h3>No incoming requests yet</h3><p>When a student requests one of your items, it will appear here.</p><a class="text-link" href="{{ route('sharing.transactions.index') }}">View transaction history →</a></div>@endforelse</div>
    @include('sharing.partials.pagination', ['paginator' => $requests])
</section>
@endsection
