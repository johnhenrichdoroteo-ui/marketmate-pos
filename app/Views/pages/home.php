<section class="hero home-hero">
    <div class="container hero-grid">
        <div class="hero-copy reveal">
            <p class="eyebrow"><span class="eyebrow-line"></span> THE EVERYDAY SALES WORKSPACE</p>
            <h1>Make every sale <em>feel in hand.</em></h1>
            <p class="lead">From the products you carry to customer records and daily sales, MarketMate keeps the moving parts of a business in one clear workspace.</p>
            <div class="actions">
                <?php if (session('auth_user_id')): ?>
                    <a class="button primary" href="<?= site_url('dashboard') ?>">Open the workspace <span aria-hidden="true">↗</span></a>
                    <a class="text-link" href="<?= site_url('sales/new') ?>">Record a sale <span aria-hidden="true">→</span></a>
                <?php else: ?>
                    <a class="button primary" href="<?= site_url('login') ?>">Staff sign in <span aria-hidden="true">↗</span></a>
                    <a class="text-link" href="<?= site_url('about') ?>">Get to know MarketMate <span aria-hidden="true">→</span></a>
                <?php endif; ?>
            </div>
            <div class="hero-footnote"><span class="hero-footnote-mark" aria-hidden="true"></span> One connected system. Fewer loose ends.</div>
        </div>
        <div class="hero-showcase reveal" aria-label="Illustration of the MarketMate sales workflow">
            <div class="showcase-orbit showcase-orbit-one"></div>
            <div class="showcase-orbit showcase-orbit-two"></div>
            <div class="showcase-window">
                <div class="showcase-topline"><span class="showcase-dots" aria-hidden="true"><i></i><i></i><i></i></span><span>MARKETMATE / CHECKOUT</span><span class="showcase-live">LIVE FLOW</span></div>
                <div class="showcase-inner">
                    <div class="showcase-heading"><div><small>CURRENT SALE</small><strong>Ready to check out</strong></div><span class="showcase-icon" aria-hidden="true">↗</span></div>
                    <div class="showcase-item"><span class="showcase-item-icon" aria-hidden="true">01</span><div><strong>Product</strong><small>Selected from catalog</small></div><span class="showcase-check">✓</span></div>
                    <div class="showcase-item"><span class="showcase-item-icon" aria-hidden="true">02</span><div><strong>Customer</strong><small>Optional at checkout</small></div><span class="showcase-check">✓</span></div>
                    <div class="showcase-total"><span>TOTAL &amp; INVENTORY</span><strong>Calculated together</strong></div>
                </div>
            </div>
            <div class="showcase-tag">SALE RECORDED <span aria-hidden="true">↗</span></div>
        </div>
    </div>
</section>

<section class="flow-strip" aria-label="MarketMate workflow"><div class="container flow-strip-inner"><span>01 / CATALOG</span><span aria-hidden="true">→</span><span>02 / CHECKOUT</span><span aria-hidden="true">→</span><span>03 / STOCK UPDATE</span><span aria-hidden="true">→</span><span>04 / SALES HISTORY</span></div></section>

<section class="section home-intro">
    <div class="container intro-grid">
        <div class="section-heading reveal"><p class="eyebrow">BUILT FOR THE COUNTER</p><h2>The day moves fast.<br><em>Your system should keep up.</em></h2></div>
        <div class="intro-copy reveal"><p>MarketMate keeps the essential parts of a point-of-sale operation connected. A recorded sale updates stock immediately, while customer and staff records stay organized behind a secure sign-in.</p><a class="text-link" href="<?= site_url('about') ?>">See how it works <span aria-hidden="true">→</span></a></div>
    </div>
    <div class="container capability-grid">
        <article class="capability capability-wide reveal"><span class="feature-number">01 / SELL</span><div><h3>From product to payment record.</h3><p>Select an item, add a customer if needed, enter the quantity, and let MarketMate calculate the total. The system stops a sale when stock is short.</p></div><span class="capability-arrow" aria-hidden="true">↗</span></article>
        <article class="capability reveal"><span class="feature-number">02 / STOCK</span><h3>Know what's ready to sell.</h3><p>Keep prices and quantities current, add product photos, and archive older items without losing their sales history.</p><span class="capability-arrow" aria-hidden="true">↗</span></article>
        <article class="capability reveal"><span class="feature-number">03 / PEOPLE</span><h3>Keep the right details close.</h3><p>Manage customers and staff accounts in one protected workspace, with roles, statuses, and staff photos.</p><span class="capability-arrow" aria-hidden="true">↗</span></article>
    </div>
</section>

<section class="closing-band"><div class="container closing-band-inner"><div><p class="eyebrow">MARKETMATE POS</p><h2>Built for the work behind every sale.</h2></div><a class="button light" href="<?= session('auth_user_id') ? site_url('dashboard') : site_url('login') ?>"><?= session('auth_user_id') ? 'Go to dashboard' : 'Enter staff workspace' ?> <span aria-hidden="true">↗</span></a></div></section>
