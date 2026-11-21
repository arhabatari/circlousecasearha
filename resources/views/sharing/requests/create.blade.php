@extends('sharing.layout')
@section('title', 'Request item')
@section('content')
<a class="back-link" href="{{ route('sharing.items.show', $item) }}">← Back to item</a>
<div class="page-heading"><div><span class="eyebrow">CIRCLO / SHARING REQUEST</span><h1>Request item</h1><p>A few details, then you're one step closer.</p></div><span class="page-tag">Sharing request</span></div>
<div class="two-column form-layout"><section class="card">@include('sharing.partials.item-summary', ['item' => $item])
    @if($item->owner_id === $actor->id)<div class="alert notice">You cannot request your own item. <a href="{{ route('sharing.items.index') }}">Explore other items.</a></div>
    @elseif($item->catalogItem->status !== 'available')<div class="alert notice">This item is currently unavailable. Please choose another item.</div>
    @else
    <form method="POST" action="{{ route('sharing.requests.store', $item) }}" class="stack-form request-form" data-price="{{ $item->catalogItem->daily_price }}">@csrf
        <h2 class="section-label">Borrow / rental period</h2>
        <div class="field-grid"><div><label for="starts_on">Start date <span aria-hidden="true">*</span></label><input type="date" id="starts_on" name="starts_on" required min="{{ today(config('circlo-sharing.timezone'))->toDateString() }}" value="{{ old('starts_on') }}" @error('starts_on') aria-invalid="true" aria-describedby="starts-error" @enderror>@error('starts_on')<p class="field-error" id="starts-error">{{ $message }}</p>@enderror</div><div><label for="ends_on">End date <span aria-hidden="true">*</span></label><input type="date" id="ends_on" name="ends_on" required min="{{ today(config('circlo-sharing.timezone'))->toDateString() }}" value="{{ old('ends_on') }}" @error('ends_on') aria-invalid="true" aria-describedby="ends-error" @enderror>@error('ends_on')<p class="field-error" id="ends-error">{{ $message }}</p>@enderror</div></div>
        <p class="field-help">Same-day requests are allowed. Both dates count toward the estimate.</p>
        <div><label for="message">Notes / message <span class="muted">(optional)</span></label><textarea id="message" name="message" rows="4" maxlength="2000" placeholder="Tell the owner what you need the item for…">{{ old('message') }}</textarea><p class="field-help">Up to 2,000 characters. Please keep personal contact details private.</p></div>
        <div class="estimate"><span>Estimated total <small data-duration>Choose your dates</small></span><strong data-total aria-live="polite">—</strong></div>
        <button class="button primary full" type="submit">Submit Request <span aria-hidden="true">→</span></button><a class="cancel-link" href="{{ route('sharing.items.show', $item) }}">Cancel and return to item</a>
    </form>@endif
</section><aside><div class="card tinted"><span class="step-number">GOOD TO KNOW</span><h2>Sharing starts with a request.</h2><ol class="journey"><li><strong>Choose your dates</strong><span>Confirmed bookings are checked when you submit.</span></li><li><strong>Wait for the owner</strong><span>Your request appears in the Pending tab.</span></li><li><strong>Get ready to share</strong><span>Once accepted, find it in Active and arrange pickup.</span></li></ol><div class="pickup-note"><span class="eyebrow">PICKUP POINT</span><p>{{ $item->pickup_location }}</p></div></div><p class="small muted inset-note">This is an estimate. No payment is taken by this demo.</p></aside></div>
@endsection
