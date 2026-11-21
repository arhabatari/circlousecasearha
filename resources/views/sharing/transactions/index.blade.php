@extends('sharing.layout')
@section('title', 'My Transactions')
@section('content')
<div class="page-heading"><div><span class="eyebrow">YOUR SHARING JOURNEY</span><h1>My Transactions</h1><p>Every request. Every shared possibility.</p></div><a class="button primary" href="{{ route('sharing.items.index') }}">Find an item <span aria-hidden="true">+</span></a></div>
<div class="stat-grid">@foreach(['pending' => 'Awaiting a decision', 'active' => 'Sharing in progress', 'completed' => 'Returned and completed'] as $key => $caption)<a class="stat-card {{ $tab === $key ? 'current' : '' }}" href="{{ route('sharing.transactions.index', ['tab' => $key]) }}"><span>{{ ucfirst($key) }}</span><strong>{{ $counts[$key] }}</strong><small>{{ $caption }}</small></a>@endforeach</div>
<section class="card transaction-card"><nav class="tabs" aria-label="Transaction status">@foreach(['pending', 'active', 'completed'] as $key)<a href="{{ route('sharing.transactions.index', ['tab' => $key]) }}" class="{{ $tab === $key ? 'active' : '' }}" @if($tab === $key) aria-current="page" @endif>{{ ucfirst($key) }} <span>{{ $counts[$key] }}</span></a>@endforeach</nav><div class="entry-list">
    @forelse($entries as $entry)<article class="entry">@include('sharing.partials.item-art', ['catalog' => $entry->item->catalogItem, 'artClass' => 'art-small'])<div class="entry-main"><span class="small muted">#{{ $entry->id }} · {{ $entry->borrower_id === $actor->id ? 'Borrowing from '.$entry->owner->name : 'Lending to '.$entry->borrower->name }}</span><h3><a href="{{ route('sharing.requests.show', $entry) }}">{{ $entry->item->catalogItem->title }}</a></h3><p>{{ $entry->starts_on->format('d M Y') }} → {{ $entry->ends_on->format('d M Y') }} · {{ $entry->days() }} {{ $entry->days() === 1 ? 'day' : 'days' }}</p>@if($tab === 'completed')<p class="small muted">Completed {{ $entry->transaction->completed_at?->format('d M Y') ?? '—' }}</p>@endif</div><div class="entry-end"><span class="badge {{ $tab }}">{{ ucfirst($tab) }}</span><strong>Rp {{ number_format($entry->total_price, 0, ',', '.') }}</strong><a class="text-link" href="{{ route('sharing.requests.show', $entry) }}">{{ $tab === 'pending' && $entry->owner_id === $actor->id ? 'Review request' : 'View details' }} →</a></div></article>
    @empty<div class="empty-state"><div class="empty-symbol" aria-hidden="true">⇄</div><h3>No {{ $tab }} {{ $tab === 'pending' ? 'requests' : 'transactions' }} yet</h3><p>{{ $tab === 'pending' ? 'Start with an item you need. Your next sharing story begins here.' : ($tab === 'active' ? 'Accepted requests will appear here for both students.' : 'Your completed sharing history will appear here after an item is returned.') }}</p><a class="button secondary" href="{{ route('sharing.items.index') }}">Explore shared items →</a></div>@endforelse
</div>@include('sharing.partials.pagination', ['paginator' => $entries])</section>
@if($decisions->isNotEmpty())
<section class="card decisions-card">
    <div class="card-heading"><div><span class="eyebrow">DECISION HISTORY</span><h2>Closed requests</h2></div><span class="small muted">{{ $decisions->total() }} rejected / cancelled</span></div>
    <p class="small muted">Closed requests do not create a completed transaction.</p>
    @foreach($decisions as $entry)
        <article class="decision-entry"><div><a class="text-link" href="{{ route('sharing.requests.show', $entry) }}">#{{ $entry->id }} · {{ $entry->item->catalogItem->title }}</a><p class="small muted">{{ $entry->starts_on->format('d M Y') }} → {{ $entry->ends_on->format('d M Y') }}</p><p class="preserve-text">{{ $entry->rejection_reason ?: 'No reason was added.' }}</p></div><span class="badge {{ $entry->status->value }}">{{ ucfirst($entry->status->value) }}</span></article>
    @endforeach
    @include('sharing.partials.pagination', ['paginator' => $decisions, 'paginationLabel' => 'Decision history pages'])
</section>
@endif
@endsection
