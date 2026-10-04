<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | MarketMate POS</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <header class="site-header">
        <div class="container nav-wrap">
            <a class="brand" href="<?= base_url('/') ?>">MarketMate <span>POS</span></a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">
                <span class="sr-only">Toggle navigation</span>
                <span aria-hidden="true">☰</span>
            </button>
            <nav id="primary-navigation" aria-label="Primary navigation">
                <a class="<?= ($activePage ?? '') === 'home' ? 'active' : '' ?>" href="<?= base_url('/') ?>">Home</a>
                <a class="<?= ($activePage ?? '') === 'about' ? 'active' : '' ?>" href="<?= base_url('about') ?>">About</a>
                <?php if (session('auth_user_id')): ?>
                    <a class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">Dashboard</a>
                    <a class="<?= ($activePage ?? '') === 'customers' ? 'active' : '' ?>" href="<?= site_url('customers') ?>">Customers</a>
                    <a class="<?= ($activePage ?? '') === 'users' ? 'active' : '' ?>" href="<?= site_url('users') ?>">Users</a>
                    <form class="nav-logout" method="post" action="<?= site_url('logout') ?>">
                        <?= csrf_field() ?>
                        <button type="submit">Log out</button>
                    </form>
                <?php else: ?>
                    <a class="nav-signin" href="<?= site_url('login') ?>">Staff sign in</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main>
