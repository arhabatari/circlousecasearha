@php($actor = request()->attributes->get('sharing_actor'))
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sharing') | Circlo</title>
    <link rel="stylesheet" href="{{ asset('css/circlo-sharing.css') }}">
    <script src="{{ asset('js/circlo-sharing.js') }}" defer></script>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to content</a>
<div class="sharing-shell">
    <aside class="sidebar" aria-label="Main navigation">
        <a class="brand" href="{{ route('landing') }}"><span class="brand-mark">C</span>CIRCLO</a>
        <p class="sidebar-caption">Good things go around.</p>
        <nav>
            <a class="nav-link" href="{{ route('home') }}"><span aria-hidden="true">⌂</span>Home</a>
            <a class="nav-link {{ request()->routeIs('sharing.items.*', 'sharing.requests.create') ? 'selected' : '' }}" href="{{ route('sharing.items.index') }}"><span aria-hidden="true">⌕</span>Explore items</a>
            <a class="nav-link {{ request()->routeIs('sharing.transactions.*') ? 'selected' : '' }}" href="{{ route('sharing.transactions.index') }}"><span aria-hidden="true">⇄</span>My Transactions</a>
            <a class="nav-link {{ request()->routeIs('sharing.requests.incoming', 'sharing.requests.show') ? 'selected' : '' }}" href="{{ route('sharing.requests.incoming') }}"><span aria-hidden="true">▤</span>Incoming requests</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="campus-note"><span class="online-dot"></span>ITS campus community</div>
            <div class="profile"><span class="avatar">{{ $actor?->initials() ?? 'C' }}</span><div><strong>{{ $actor?->name ?? 'Choose a student' }}</strong><small>Local classroom demo</small></div></div>
        </div>
    </aside>
    <div class="workspace">
        <header class="topbar"><span>STUDENT PORTAL <span class="crumb-divider">/</span> <span class="desktop-section">SHARING</span><span class="mobile-student">{{ $actor?->name ?? 'Sharing demo' }}</span></span><a class="switch-link" href="{{ route('sharing.demo') }}">Switch demo student <span aria-hidden="true">↗</span></a></header>
        <div class="demo-banner"><strong>DEMO MODE</strong><span>Students are selected for testing. This is not a login or production authentication.</span>@if($actor)<form method="POST" action="{{ route('sharing.demo.end') }}">@csrf<button class="text-button" type="submit">End demo session</button></form>@endif</div>
        <main id="main-content" tabindex="-1">
            @foreach(['success', 'notice'] as $flash)
                @if(session($flash))<div class="alert {{ $flash }}" role="status">{{ session($flash) }}</div>@endif
            @endforeach
            @if($errors->any())
                <div class="alert error" role="alert"><strong>Please check the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @yield('content')
        </main>
        <footer>Share more. Spend less. Connect on campus.<span>Circlo campus community</span></footer>
    </div>
</div>
</body>
</html>
