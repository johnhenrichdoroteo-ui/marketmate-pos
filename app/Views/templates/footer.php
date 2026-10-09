    </main>
    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-intro">
                <a class="brand" href="<?= site_url('/') ?>" aria-label="MarketMate POS home">
                    <img class="brand-symbol" src="<?= base_url('assets/images/marketmate-mark.svg') ?>" alt="" width="42" height="42">
                    <span class="brand-name">MarketMate <small>POINT OF SALE</small></span>
                </a>
                <p>A practical place to manage what you sell, the people you serve, and the sales that keep business moving.</p>
            </div>
            <div class="footer-nav">
                <span class="footer-label">Explore</span>
                <a href="<?= site_url('about') ?>">About MarketMate</a>
                <?php if (session('auth_user_id')): ?>
                    <a href="<?= site_url('dashboard') ?>">Staff workspace</a>
                    <a href="<?= site_url('sales/new') ?>">Record a sale</a>
                <?php else: ?>
                    <a href="<?= site_url('login') ?>">Staff sign in</a>
                <?php endif; ?>
            </div>
            <div class="footer-note">
                <span class="footer-label">Built by</span>
                <strong>John Henrich Doroteo</strong>
                <span>BSIT · Business Analytics</span>
            </div>
        </div>
        <div class="container footer-bottom"><span>© MarketMate POS</span><span>Made for IT0049 Web System Technologies</span></div>
    </footer>
    <script src="<?= base_url('assets/js/app.js') ?>" defer></script>
</body>
</html>
