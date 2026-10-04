<section class="dashboard-hero">
    <div class="container dashboard-hero-inner">
        <div>
            <p class="eyebrow">Staff workspace</p>
            <h1>Good to see you, <?= esc($displayName) ?>.</h1>
            <p class="lead">Keep the important account details close and the day moving.</p>
        </div>
        <span class="dashboard-status">Signed in</span>
    </div>
</section>

<section class="section dashboard-section">
    <div class="container">
        <div class="section-heading">
            <p class="eyebrow">Your overview</p>
            <h2>Start where you need to.</h2>
            <p class="lead">Choose an account directory to view, add, or update records.</p>
        </div>
        <div class="dashboard-grid">
            <article class="dashboard-card">
                <span class="dashboard-card-index">01 / Customers</span>
                <strong class="dashboard-count"><?= esc($customerCount) ?></strong>
                <h3>Customer accounts</h3>
                <p>Contact information, membership type, and account status.</p>
                <a class="dashboard-link" href="<?= site_url('customers') ?>">Open customers <span aria-hidden="true">→</span></a>
            </article>
            <article class="dashboard-card">
                <span class="dashboard-card-index">02 / Team</span>
                <strong class="dashboard-count"><?= esc($userCount) ?></strong>
                <h3>User accounts</h3>
                <p>Staff profiles, assigned roles, and profile pictures.</p>
                <a class="dashboard-link" href="<?= site_url('users') ?>">Open users <span aria-hidden="true">→</span></a>
            </article>
        </div>
    </div>
</section>
