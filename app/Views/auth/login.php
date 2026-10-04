<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | MarketMate POS</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="auth-page">
    <div class="auth-wrap">
        <header class="auth-topbar">
            <a class="auth-brand" href="<?= base_url('/') ?>">MarketMate <span>POS</span></a>
            <a class="auth-back" href="<?= base_url('/') ?>">Back to home</a>
        </header>

        <main class="auth-layout">
            <section class="auth-panel" aria-labelledby="login-title">
                <p class="eyebrow">Staff workspace</p>
                <h1 id="login-title">Welcome back.</h1>
                <p class="auth-subtitle">Sign in to manage the people behind your business.</p>

                <?php if (session('auth_notice')): ?>
                    <p class="notice" role="status"><?= esc(session('auth_notice')) ?></p>
                <?php endif; ?>
                <?php if (session('auth_error')): ?>
                    <p class="form-alert" role="alert"><?= esc(session('auth_error')) ?></p>
                <?php endif; ?>

                <form method="post" action="<?= site_url('login') ?>" class="auth-form">
                    <?= csrf_field() ?>
                    <div class="form-field">
                        <label for="username">Username</label>
                        <input id="username" name="username" type="text" autocomplete="username" maxlength="50" required autofocus value="<?= esc((string) (session('login_username') ?? ''), 'attr') ?>">
                    </div>
                    <div class="form-field">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required>
                    </div>
                    <button class="button primary auth-submit" type="submit">Sign in</button>
                </form>
                <p class="auth-help">For staff accounts only. Ask your administrator if you need access.</p>
            </section>

            <aside class="auth-visual" aria-label="MarketMate workspace preview">
                <div class="auth-visual-copy">
                    <p class="auth-visual-kicker">MarketMate POS</p>
                    <h2>Make room for a smoother day.</h2>
                    <p>Customers and staff, organized in one dependable workspace.</p>
                </div>
                <div class="auth-image-slot">
                    <img src="<?= base_url('assets/images/marketmate-login-custom.jpg') ?>"
                         alt="Small shop counter with a point-of-sale terminal and products">
                </div>
                <p class="auth-visual-foot">A little more clarity for every business day.</p>
            </aside>
        </main>
    </div>
</body>
</html>
