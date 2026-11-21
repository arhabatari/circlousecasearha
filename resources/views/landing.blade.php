<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Circlo: borrow and share everyday essentials across your campus community.">
    <title>Circlo | Borrow more, share more</title>
    @vite('resources/css/app.css')
    <style>
        /* Public landing: original Circlo navy / blue design system.
           Scoped with .public-landing to avoid changing the existing /home UI. */
        .public-landing {--navy:#1a344e;--blue:#306cee;--sky:#eaf1ff;--line:#dbe5f0;--muted:#667e99;--paper:#f6f8fc;color:var(--navy);background:#fff;font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',sans-serif;min-height:100vh}
        .public-landing *{box-sizing:border-box}.public-landing a{text-decoration:none;color:inherit}.public-landing button{font:inherit}.public-landing .wrap{max-width:1160px;margin:0 auto;padding-left:26px;padding-right:26px}
        .public-landing .topnav{background:#fff;border-bottom:1px solid #e9eef4;position:sticky;top:0;z-index:5}.public-landing .navinner{height:76px;display:flex;align-items:center;justify-content:space-between;gap:22px}
        .public-landing .logo{font-size:25px;font-weight:850;letter-spacing:-1.2px;color:var(--navy);display:flex;align-items:center;gap:10px}.public-landing .logo-symbol{width:34px;height:34px;border-radius:11px;display:grid;place-items:center;background:var(--blue);color:#fff;font-size:21px;font-weight:900}
        .public-landing .links{display:flex;align-items:center;gap:33px}.public-landing .links a{font-size:13px;font-weight:650;color:#506983}.public-landing .links a:hover{color:var(--blue)}
        .public-landing .nav-actions{display:flex;align-items:center;gap:13px}.public-landing .login{font-size:13px;font-weight:700}.public-landing .btn{border:0;display:inline-flex;align-items:center;justify-content:center;gap:9px;min-height:45px;padding:0 21px;border-radius:11px;font-size:13px;font-weight:760;transition:transform .15s,background .15s}.public-landing .btn:hover{transform:translateY(-1px)}.public-landing .btn-primary{background:var(--blue);color:white;box-shadow:0 8px 20px #306cee30}.public-landing .btn-soft{color:var(--navy);background:#fff;border:1px solid #d9e3ee}.public-landing .menu-toggle{display:none}
        .public-landing .hero{background:radial-gradient(circle at 83% 45%,#eaf1ff 0%,#f8faff 47%,#fff 80%);overflow:hidden}.public-landing .hero-layout{display:grid;grid-template-columns:1.06fr .94fr;min-height:545px;gap:30px;align-items:center;padding-top:55px;padding-bottom:56px}.public-landing .eyebrow{display:flex;align-items:center;gap:8px;color:var(--blue);font-size:11px;letter-spacing:1.35px;font-weight:850}.public-landing .eyebrow i{background:#ffb784;width:8px;height:8px;border-radius:50%;display:block}.public-landing h1{font-size:clamp(43px,4.8vw,68px);line-height:1.08;letter-spacing:-2.9px;margin:22px 0 20px;font-weight:850}.public-landing h1 em{color:var(--blue);font-style:normal}.public-landing .lead{color:var(--muted);font-size:15px;line-height:1.85;max-width:510px;margin:0 0 29px}.public-landing .hero-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap}.public-landing .hero-note{margin-top:27px;display:flex;align-items:center;gap:11px;color:#7790a9;font-size:12px}.public-landing .hero-note svg{color:#3bba86}
        .public-landing .illustration{position:relative;min-height:420px;display:grid;place-items:center}.public-landing .orbit{position:absolute;inset:26px 9px 14px;border:1px solid #d6e2f4;border-radius:50%;transform:rotate(-13deg)}.public-landing .orbit:before{content:'';position:absolute;inset:28px;border:1px dashed #c3d4ec;border-radius:50%}.public-landing .ill-card{position:relative;background:#fff;padding:12px;border-radius:26px;transform:rotate(-7deg);box-shadow:0 22px 66px #123c741b;width:min(335px,75%)}.public-landing .ill-art{height:290px;border-radius:18px;background:linear-gradient(145deg,#dce8fd,#c2d8fb);display:grid;place-items:center;overflow:hidden}.public-landing .ill-art svg{width:240px;max-width:90%}.public-landing .floating-chip{position:absolute;background:#fff;padding:13px 16px;box-shadow:0 12px 35px #123c7418;border:1px solid #f0f2f8;border-radius:12px;font-size:12px;font-weight:800;white-space:nowrap}.public-landing .chip-one{right:-13px;top:81px;transform:rotate(4deg)}.public-landing .chip-two{left:-12px;bottom:56px;transform:rotate(-5deg)}.public-landing .chip-dot{display:inline-grid;place-items:center;width:26px;height:26px;border-radius:50%;background:#eaf2ff;color:var(--blue);margin-right:5px}
        .public-landing .stats{background:var(--navy);color:#fff}.public-landing .stats-inner{display:flex;align-items:center;justify-content:space-between;gap:25px;padding-top:20px;padding-bottom:20px}.public-landing .stat-main{font-size:14px;font-weight:750}.public-landing .stat-main strong{font-size:20px;color:#9ec0ff}.public-landing .stat-caption{font-size:11px;color:#b1c4d6}
        .public-landing .section{padding-top:74px;padding-bottom:77px}.public-landing .section.muted{background:var(--paper)}.public-landing .section-heading{display:flex;justify-content:space-between;align-items:end;gap:16px;margin-bottom:27px}.public-landing .kicker{font-size:11px;color:var(--blue);font-weight:850;letter-spacing:1.2px;text-transform:uppercase}.public-landing h2{font-size:32px;letter-spacing:-1.25px;line-height:1.2;margin:10px 0 9px}.public-landing .section-description{color:var(--muted);font-size:13px;margin:0;line-height:1.75}.public-landing .text-link{color:var(--blue);font-weight:760;font-size:13px;white-space:nowrap}
        .public-landing .cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}.public-landing .item{border:1px solid #e6ebf2;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 25px #19374c06}.public-landing .item-visual{height:170px;display:grid;place-items:center;background:#e6f0ff;position:relative}.public-landing .item-visual.books{background:#f5ecff}.public-landing .item-visual.equipment{background:#e8f8ef}.public-landing .item-visual.fashion{background:#fff0e6}.public-landing .item-emoji{font-size:70px;line-height:1}.public-landing .item-pill{position:absolute;right:13px;top:13px;background:#e4f6eb;color:#238754;border-radius:99px;font-size:10px;font-weight:800;padding:7px 10px}.public-landing .item-detail{padding:18px 18px 21px}.public-landing .item-cat{font-size:10px;text-transform:uppercase;letter-spacing:1px;font-weight:850;color:#849ab0}.public-landing .item h3{font-size:17px;margin:7px 0 11px;letter-spacing:-.4px}.public-landing .item-bottom{display:flex;justify-content:space-between;align-items:center;gap:8px}.public-landing .price{color:var(--blue);font-size:13px;font-weight:850}.public-landing .owner{font-size:11px;color:var(--muted)}.public-landing .card-action{display:inline-block;margin-top:16px;color:var(--blue);font-size:12px;font-weight:850}.public-landing .empty{grid-column:1/-1;padding:26px;background:#fff;border:1px solid var(--line);border-radius:16px;color:var(--muted)}
        .public-landing .steps{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:22px}.public-landing .step{padding:28px 25px;background:#fff;border-radius:17px;border:1px solid #e5ebf2}.public-landing .step-no{width:50px;height:50px;border-radius:14px;background:#e9f0ff;color:var(--blue);display:grid;place-items:center;font-weight:850;font-size:19px}.public-landing .step h3{margin:19px 0 10px;font-size:17px}.public-landing .step p{font-size:13px;color:var(--muted);line-height:1.7;margin:0}
        .public-landing .cta{background:var(--blue);border-radius:22px;padding:42px 48px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:25px}.public-landing .cta h2{color:#fff;font-size:31px}.public-landing .cta p{color:#dbe7ff;font-size:13px;max-width:470px;line-height:1.7}.public-landing .cta .btn{background:#fff;color:var(--navy);flex-shrink:0}.public-landing .footer{padding:25px 0;border-top:1px solid #e5ebf2;color:#738aa2;font-size:12px}.public-landing .footerinner{display:flex;justify-content:space-between;gap:16px;align-items:center}
        @media(max-width:930px){.public-landing .hero-layout{grid-template-columns:1fr .85fr}.public-landing .ill-card{width:89%}.public-landing .floating-chip{font-size:10px}.public-landing .links{gap:15px}.public-landing h1{letter-spacing:-1.7px}.public-landing .cards{gap:12px}}
        @media(max-width:700px){.public-landing .wrap{padding-left:18px;padding-right:18px}.public-landing .navinner{height:64px}.public-landing .logo{font-size:22px}.public-landing .links{display:none}.public-landing .nav-actions .login{display:none}.public-landing .nav-actions .btn{min-height:39px;padding:0 11px;font-size:11px}.public-landing .hero-layout{display:flex;flex-direction:column;align-items:stretch;padding-top:42px;padding-bottom:36px;gap:0}.public-landing .hero-layout>div:first-child{max-width:560px}.public-landing h1{font-size:49px}.public-landing .lead{font-size:14px}.public-landing .illustration{min-height:315px;margin:8px 0 -6px}.public-landing .ill-card{width:225px;padding:9px;border-radius:20px}.public-landing .ill-art{height:232px}.public-landing .chip-one{right:9px;top:51px}.public-landing .chip-two{left:2px;bottom:48px}.public-landing .stats-inner{align-items:start;flex-direction:column;gap:5px}.public-landing .section{padding-top:55px;padding-bottom:55px}.public-landing .cards,.public-landing .steps{grid-template-columns:1fr}.public-landing .section-heading{align-items:start}.public-landing h2{font-size:28px}.public-landing .cta{padding:27px;display:block}.public-landing .cta .btn{margin-top:16px}.public-landing .footerinner{flex-direction:column;align-items:start}}
    </style>
</head>
<body class="public-landing">
    <header class="topnav">
        <div class="wrap navinner">
            <a href="{{ route('landing') }}" class="logo" aria-label="Circlo home"><span class="logo-symbol">C</span>CIRCLO</a>
            <nav class="links" aria-label="Primary navigation">
                <a href="#how-it-works">How it works</a>
                <a href="#featured">Explore items</a>
                <a href="#community">Our community</a>
            </nav>
            <div class="nav-actions">
                <a class="login" href="{{ route('home') }}">Student portal</a>
                <a class="btn btn-primary" href="{{ route('home') }}">Join Circlo <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </header>
    <main>
        <section class="hero">
            <div class="wrap hero-layout">
                <div>
                    <div class="eyebrow"><i></i> GOOD THINGS GO AROUND</div>
                    <h1>A little more <em>sharing.</em><br>A lot more living.</h1>
                    <p class="lead">The things we need are already close by. Borrow from your fellow students, share what you have, and make campus life feel a little more connected.</p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="#featured">Explore shared items <span aria-hidden="true">→</span></a>
                        <a class="btn btn-soft" href="#how-it-works">See how it works</a>
                    </div>
                    <div class="hero-note">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="m8 12 3 3 5-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        A student-sharing concept designed for campus life
                    </div>
                </div>
                <div class="illustration" aria-label="Illustration of items available to share">
                    <div class="orbit"></div>
                    <div class="ill-card">
                        <div class="ill-art">
                            <svg viewBox="0 0 260 260" role="img" aria-label="Blue backpack illustration">
                                <ellipse cx="132" cy="228" rx="91" ry="14" fill="#a2bce7" opacity=".35"/>
                                <path d="M83 111V83c0-27 19-47 47-47s47 20 47 47v28" stroke="#204969" stroke-width="17" fill="none" stroke-linecap="round"/>
                                <rect x="52" y="85" width="156" height="156" rx="38" fill="#306cee"/>
                                <path d="M52 160c36 9 73 13 156 0v45c0 20-16 36-36 36H88c-20 0-36-16-36-36z" fill="#2358c4"/>
                                <rect x="71" y="119" width="118" height="60" rx="15" fill="#6e9eff"/>
                                <rect x="87" y="135" width="86" height="8" rx="4" fill="#cfe0ff"/>
                                <rect x="113" y="173" width="34" height="21" rx="9" fill="#193d68"/>
                                <rect x="27" y="117" width="27" height="74" rx="12" fill="#1a4caa"/>
                                <rect x="206" y="117" width="27" height="74" rx="12" fill="#1a4caa"/>
                                <circle cx="130" cy="183" r="5" fill="#fbca83"/>
                            </svg>
                        </div>
                    </div>
                    <div class="floating-chip chip-one"><span class="chip-dot">↗</span> Shared nearby</div>
                    <div class="floating-chip chip-two"><span class="chip-dot">✓</span> Less buying, more belonging</div>
                </div>
            </div>
        </section>

        <section class="stats" id="community">
            <div class="wrap stats-inner">
                <div class="stat-main"><strong>{{ number_format($availableCount) }}</strong> sample items currently available</div>
                <div class="stat-caption">Live count from the local PostgreSQL demo database</div>
            </div>
        </section>

        <section class="section" id="featured">
            <div class="wrap">
                <div class="section-heading">
                    <div><div class="kicker">Featured on Circlo</div><h2>Discover what's around you</h2><p class="section-description">Real database records displayed through Laravel and Blade.</p></div>
                    <a class="text-link" href="{{ route('home') }}">Preview student portal →</a>
                </div>
                <div class="cards">
                    @forelse ($featuredItems as $item)
                        @php
                            $emoji = match ($item->category) {
                                'Electronics' => '💻',
                                'Books' => '📚',
                                'Equipment' => '⛺',
                                'Fashion' => '🧥',
                                'Sports' => '🚲',
                                default => '📦',
                            };
                        @endphp
                        <article class="item">
                            <div class="item-visual {{ strtolower($item->category) }}">
                                <span class="item-emoji" aria-hidden="true">{{ $emoji }}</span>
                                <span class="item-pill">Available</span>
                            </div>
                            <div class="item-detail">
                                <div class="item-cat">{{ $item->category }}</div>
                                <h3>{{ $item->title }}</h3>
                                <div class="item-bottom"><span class="price">Rp {{ number_format($item->daily_price, 0, ',', '.') }}/day</span><span class="owner">{{ $item->owner }}</span></div>
                                <a class="card-action" href="{{ route('home') }}">View student portal →</a>
                            </div>
                        </article>
                    @empty
                        <p class="empty">No demo items found. Run <code>php artisan db:seed --class=CircloDemoItemSeeder</code> to add example records.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section muted" id="how-it-works">
            <div class="wrap">
                <div class="kicker">How it works</div><h2>Sharing made simple</h2><p class="section-description" style="margin-bottom:26px">Three simple steps to make more use of what our community already owns.</p>
                <div class="steps">
                    <div class="step"><div class="step-no">01</div><h3>Discover an item</h3><p>Explore useful items shared by students, from books and gadgets to everyday campus essentials.</p></div>
                    <div class="step"><div class="step-no">02</div><h3>Request to borrow</h3><p>Choose an item and arrange the borrowing period. Request-handling features will be added in a later phase.</p></div>
                    <div class="step"><div class="step-no">03</div><h3>Share responsibly</h3><p>Return items respectfully and keep the sharing circle going for the next student.</p></div>
                </div>
            </div>
        </section>

        <section class="section"><div class="wrap"><div class="cta"><div><div class="kicker" style="color:#dce9ff">Good things go around</div><h2>Make your campus more circular.</h2><p>Discover what a connected sharing community could look like. Open the student dashboard prototype to explore the next part of Circlo.</p></div><a class="btn" href="{{ route('home') }}">View student demo →</a></div></div></section>
    </main>
    <footer class="footer"><div class="wrap footerinner"><strong>CIRCLO · Borrow. Share. Make it count.</strong><span>University project prototype · Sample item data</span></div></footer>
</body>
</html>
