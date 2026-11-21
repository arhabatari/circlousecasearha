<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Circlo | Home</title>
    @vite('resources/css/app.css')
</head>
<body>
<div class="circlo-layout">
    <div class="mobile-backdrop" id="mobileBackdrop"></div>

    <!-- Original Circlo UI-02 side navigation -->
    <aside class="sidebar" id="sidebar" aria-label="Main navigation">
        <div class="brand">
            <a href="{{ route('home') }}" class="brand-name">CIRCLO</a>
            <span>Student portal</span>
        </div>

        <nav class="side-nav">
            <a class="nav-item active" href="{{ route('home') }}" aria-current="page">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                <span>Home</span>
            </a>
            <a class="nav-item" href="{{ route('home') }}#categories">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="m15.5 15.5 5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                <span>Explore</span>
            </a>
            <a class="nav-item" href="{{ route('sharing.transactions.index') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M7 12h10M4 17h16" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"/></svg>
                <span>My Transactions</span>
            </a>
            <a class="nav-item" href="{{ route('sharing.requests.incoming') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="15" rx="2"/><path d="M3 9h18" fill="none" stroke="#19344f" stroke-width="1.2"/></svg>
                <span>My Items</span>
            </a>
        </nav>

        <div class="sidebar-profile">
            <div class="avatar avatar-sidebar">{{ $studentInitials }}</div>
            <div class="profile-copy"><strong>{{ $studentName }}</strong><span>{{ $credits }} Credits</span></div>
        </div>
    </aside>

    <div class="workspace">
        <!-- Bar across the top of the Circlo Home Page -->
        <header class="topbar">
            <button type="button" class="mobile-menu" id="mobileToggle" aria-label="Open navigation menu" aria-expanded="false">
                <svg viewBox="0 0 24 24" width="21" height="21" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
            <form class="search-form" method="GET" action="{{ route('home') }}" role="search">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6" fill="none" stroke="currentColor" stroke-width="2"/><path d="m15 15 5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                <input name="q" type="search" value="{{ $search }}" placeholder="Search items, categories, or owners..." aria-label="Search listings">
                @if($selectedCategory !== 'All')<input type="hidden" name="category" value="{{ $selectedCategory }}">@endif
            </form>
            <button class="notification-button" type="button" aria-label="Notifications (demo)" title="Notifications will be implemented later">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="avatar avatar-top" title="{{ $studentName }}">{{ $studentInitials }}</div>
        </header>

        <main class="main-content">
            <section class="welcome-banner" aria-label="Welcome to Circlo">
                <div>
                    <h1>Good evening, {{ $studentName }}</h1>
                    <p>Borrow, share, make it count. {{ $totalAvailable }} items available near you.</p>
                </div>
                <a class="explore-button" href="#categories">Explore now</a>
            </section>

            <section class="category-strip" id="categories" aria-label="Item categories">
                @foreach($categories as $category)
                    <a href="{{ route('home', array_filter(['category' => $category === 'All' ? null : $category, 'q' => $search ?: null])) }}#recommendations"
                       class="category-pill {{ $selectedCategory === $category ? 'selected' : '' }}"
                       @if($selectedCategory === $category) aria-current="true" @endif>
                        {{ $category }}
                    </a>
                @endforeach
            </section>

            <section class="recommendations" id="recommendations">
                <div class="section-header">
                    <h2>Recommended for you</h2>
                    <a href="{{ route('home') }}#recommendations">See all</a>
                </div>

                @if($listings->isEmpty())
                    <div class="empty-state">No items match your search. Try another keyword or category.</div>
                @else
                    <div class="listing-grid">
                        @foreach($listings as $item)
                            <article class="listing-card">
                                <div class="listing-image" aria-label="Image placeholder for {{ $item['title'] }}">
                                    <svg viewBox="0 0 26 26" aria-hidden="true"><circle cx="9" cy="9" r="3"/><path d="M4 21 12 13l4 4 3-3 5 7"/></svg>
                                </div>
                                <div class="listing-body">
                                    <div class="listing-title-row">
                                        <h3>{{ $item['title'] }}</h3>
                                        <span class="status {{ $item['status'] === 'Available' ? 'available' : 'booked' }}">{{ $item['status'] }}</span>
                                    </div>
                                    <p class="listing-price">{{ $item['price'] }}</p>
                                    <div class="listing-owner"><span class="owner-initials">{{ $item['initials'] }}</span><span>{{ $item['owner'] }}</span></div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
            <p class="demo-note">Task 6 UI demonstration · Listings and totals shown are sample data.</p>
        </main>
    </div>
</div>
<script>
    const toggle = document.getElementById('mobileToggle');
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('mobileBackdrop');
    const closeSidebar = () => {
        document.body.classList.remove('sidebar-open');
        toggle.setAttribute('aria-expanded', 'false');
    };
    toggle.addEventListener('click', () => {
        const isOpen = document.body.classList.toggle('sidebar-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
    });
    backdrop.addEventListener('click', closeSidebar);
    sidebar.querySelectorAll('a').forEach(a => a.addEventListener('click', closeSidebar));
</script>
</body>
</html>
