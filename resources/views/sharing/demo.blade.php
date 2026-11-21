@extends('sharing.layout')
@section('title', 'Select a demo student')
@section('content')
<div class="page-heading"><div><span class="eyebrow">CIRCLO CLASSROOM DEMO</span><h1>See both sides of sharing.</h1><p>Select a student to try the borrower and owner flows.</p></div><span class="page-tag">Demo actors</span></div>
<div class="two-column">
    <section class="card"><h2>Choose your demo student</h2><p class="muted">Switch students whenever you want to review the same request from the other side.</p>
        @if($actors->isEmpty())<div class="empty-state"><h3>No demo students yet</h3><p>Run the Circlo sharing migration and demo seeder using the installation guide.</p></div>@else
        <form method="POST" action="{{ route('sharing.demo.switch') }}" class="stack-form">@csrf
            <label for="actor_id">Demo student</label><select id="actor_id" name="actor_id" required>@foreach($actors as $student)<option value="{{ $student->id }}" @selected((int) session('circlo_sharing_actor_id') === $student->id)>{{ $student->name }} · {{ $student->student_number }}</option>@endforeach</select>
            <button class="button primary full" type="submit">Continue as demo student <span aria-hidden="true">→</span></button>
        </form>@endif
    </section>
    <aside class="card tinted"><span class="step-number">01 → 02 → 03</span><h2>A complete sharing journey</h2><ol class="journey"><li><strong>Dyandra submits a request</strong><span>Explore items, choose a period and add a message.</span></li><li><strong>The item owner reviews it</strong><span>Switch to Budi for the laptop, or Dewi for the textbook.</span></li><li><strong>Both students see the result</strong><span>Accepted requests appear in Active. Rejected requests show the owner's decision.</span></li></ol><p class="small muted">Fictional students and sample history. These sessions do not verify identity and do not log you into a real account.</p></aside>
</div>
@endsection
