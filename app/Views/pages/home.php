<section class="hero">
    <div class="container hero-grid">
        <div>
            <p class="eyebrow">Your everyday business helper</p>
            <h1>Run your business with a mate you can count on.</h1>
            <p class="lead">
                MarketMate POS is a simple point-of-sale workspace for keeping customer
                and staff information organized as your business grows.
            </p>
            <div class="actions">
                <?php if (session('auth_user_id')): ?>
                    <a class="button primary" href="<?= site_url('dashboard') ?>">Open workspace</a>
                    <a class="button secondary" href="<?= site_url('customers') ?>">View customers</a>
                <?php else: ?>
                    <a class="button primary" href="<?= site_url('login') ?>">Staff sign in</a>
                    <a class="button secondary" href="<?= base_url('about') ?>">Learn about MarketMate</a>
                <?php endif; ?>
            </div>
        </div>
        <aside class="hero-card">
            <p class="card-label">MarketMate Overview</p>
            <div class="metric"><strong>01</strong><span>Staff workspace</span></div>
            <div class="metric"><strong>02</strong><span>Account directories</span></div>
            <div class="metric"><strong>POS</strong><span>Built for daily selling</span></div>
        </aside>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <p class="eyebrow">Core pages</p>
            <h2>A calm starting point for busy sellers</h2>
        </div>
        <div class="feature-grid">
            <article class="feature-card">
                <span class="feature-number">01</span>
                <h3>Customer Accounts</h3>
                <p>Signed-in staff can keep customer contact details accurate and easy to find.</p>
            </article>
            <article class="feature-card">
                <span class="feature-number">02</span>
                <h3>User Accounts</h3>
                <p>Manage staff profiles, roles, and account access in one place.</p>
            </article>
            <article class="feature-card">
                <span class="feature-number">03</span>
                <h3>Protected workspace</h3>
                <p>Staff sign in before opening account records; public pages stay easy to browse.</p>
            </article>
        </div>
    </div>
</section>
