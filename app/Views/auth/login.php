<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0d192a">
    <meta name="description" content="Sign in to the MarketMate POS staff workspace.">
    <title><?= esc($title) ?> | MarketMate POS</title>
    <link rel="icon" href="<?= base_url('assets/images/marketmate-mark.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="auth-page">
    <div class="auth-wrap">
        <header class="auth-topbar">
            <a class="brand" href="<?= base_url('/') ?>" aria-label="MarketMate POS home">
                <img class="brand-symbol" src="<?= base_url('assets/images/marketmate-mark.svg') ?>" alt="" width="42" height="42">
                <span class="brand-name">MarketMate <small>POINT OF SALE</small></span>
            </a>
            <a class="auth-back" href="<?= base_url('/') ?>">← Back to site</a>
        </header>

        <main class="auth-layout">
            <section class="auth-panel" aria-labelledby="login-title">
                <p class="auth-overline"><span aria-hidden="true"></span> STAFF ACCESS</p>
                <h1 id="login-title">Pick up where<br><em>you left off.</em></h1>
                <p class="auth-subtitle">Sign in to manage your catalog, record sales, and keep your team in sync.</p>

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
                    <button class="button primary auth-submit" type="submit">Enter workspace <span aria-hidden="true">↗</span></button>
                </form>
                <p class="auth-help">Staff accounts only. Ask your administrator if you need access.</p>
            </section>

            <aside class="auth-visual" aria-label="What the MarketMate workspace connects">
                <div class="auth-visual-topline"><span>MARKETMATE / POS</span><span>STAFF WORKSPACE</span></div>
                <div class="auth-visual-copy">
                    <span class="auth-visual-rule" aria-hidden="true"></span>
                    <h2>One place for<br><em>the work behind every sale.</em></h2>
                    <p>From the first item on the shelf to the final sale of the day, keep the important details together.</p>
                </div>
                <div class="auth-workflow">
                    <div><span>01</span><strong>Catalog &amp; stock</strong><span aria-hidden="true">↗</span></div>
                    <div><span>02</span><strong>People &amp; team</strong><span aria-hidden="true">↗</span></div>
                    <div><span>03</span><strong>Sales &amp; history</strong><span aria-hidden="true">↗</span></div>
                </div>
            </aside>
        </main>
    </div>
</body>
</html>
