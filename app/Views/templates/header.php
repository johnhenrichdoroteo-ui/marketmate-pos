<?php $isSignedIn = (bool) session('auth_user_id'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0d192a">
    <meta name="description" content="MarketMate POS keeps products, customers, staff, and sales connected in one practical business workspace.">
    <title><?= esc($title ?? 'Workspace') ?> · MarketMate POS</title>
    <link rel="icon" href="<?= base_url('assets/images/marketmate-mark.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="site-body <?= $isSignedIn ? 'site-body--staff' : 'site-body--guest' ?>">
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header">
        <div class="nav-wrap">
            <a class="brand" href="<?= site_url('/') ?>" aria-label="MarketMate POS home">
                <img class="brand-symbol" src="<?= base_url('assets/images/marketmate-mark.svg') ?>" alt="" width="42" height="42">
                <span class="brand-name">MarketMate <small>POINT OF SALE</small></span>
            </a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
            <nav id="primary-navigation" class="primary-navigation" aria-label="Primary navigation">
                <?php if ($isSignedIn): ?>
                    <p class="nav-section-label">Workspace</p>
                <?php endif; ?>
                <div class="nav-links">
                    <?php if ($isSignedIn): ?>
                        <a href="<?= site_url('dashboard') ?>" <?= ($activePage ?? '') === 'dashboard' ? 'aria-current="page"' : '' ?>>Overview</a>
                        <a href="<?= site_url('products') ?>" <?= ($activePage ?? '') === 'products' ? 'aria-current="page"' : '' ?>>Products</a>
                        <a href="<?= site_url('sales') ?>" <?= ($activePage ?? '') === 'sales' ? 'aria-current="page"' : '' ?>>Sales</a>
                        <a href="<?= site_url('customers') ?>" <?= ($activePage ?? '') === 'customers' ? 'aria-current="page"' : '' ?>>Customers</a>
                        <a href="<?= site_url('users') ?>" <?= ($activePage ?? '') === 'users' ? 'aria-current="page"' : '' ?>>Team</a>
                    <?php else: ?>
                        <a href="<?= site_url('/') ?>" <?= ($activePage ?? '') === 'home' ? 'aria-current="page"' : '' ?>>Home</a>
                        <a href="<?= site_url('about') ?>" <?= ($activePage ?? '') === 'about' ? 'aria-current="page"' : '' ?>>About</a>
                    <?php endif; ?>
                </div>
                <?php if ($isSignedIn): ?>
                    <p class="nav-section-label nav-section-secondary">Discover</p>
                    <div class="nav-links nav-links-secondary">
                        <a href="<?= site_url('/') ?>" <?= ($activePage ?? '') === 'home' ? 'aria-current="page"' : '' ?>>Home</a>
                        <a href="<?= site_url('about') ?>" <?= ($activePage ?? '') === 'about' ? 'aria-current="page"' : '' ?>>About</a>
                    </div>
                <?php endif; ?>
                <div class="nav-actions">
                    <?php if ($isSignedIn): ?>
                        <a class="nav-cta" href="<?= site_url('sales/new') ?>">New sale <span aria-hidden="true">↗</span></a>
                        <form class="nav-logout" method="post" action="<?= site_url('logout') ?>">
                            <?= csrf_field() ?>
                            <button type="submit">Log out</button>
                        </form>
                    <?php else: ?>
                        <a class="nav-cta" href="<?= site_url('login') ?>">Staff sign in <span aria-hidden="true">↗</span></a>
                    <?php endif; ?>
                </div>
            </nav>
            <?php if ($isSignedIn): ?>
                <div class="nav-account">
                    <span class="nav-account-label">SIGNED IN AS</span>
                    <strong><?= esc((string) session('auth_display_name')) ?></strong>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <main id="main-content">
