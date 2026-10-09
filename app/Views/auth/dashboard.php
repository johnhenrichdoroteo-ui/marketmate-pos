<section class="dashboard-hero"><div class="container dashboard-hero-inner">
    <div><p class="eyebrow"><span class="eyebrow-line"></span> YOUR WORKSPACE</p><h1>Good to see you,<br><em><?= esc($displayName) ?>.</em></h1><p class="lead">A clear view of your business, from stock on hand to the latest sale.</p></div>
    <a class="button primary" href="<?= site_url('sales/new') ?>">Record a sale <span aria-hidden="true">↗</span></a>
</div></section>

<section class="section dashboard-section"><div class="container">
    <div class="dashboard-topline"><div><p class="eyebrow">AT A GLANCE</p><h2>Today's workspace</h2></div><span class="dashboard-status"><span></span> Staff session active</span></div>
    <div class="stats-grid">
        <a class="stat-tile" href="<?= site_url('sales') ?>"><span>Total sales</span><strong><?= number_format((int) $saleCount) ?></strong><small>All recorded transactions <span aria-hidden="true">↗</span></small></a>
        <a class="stat-tile" href="<?= site_url('sales') ?>"><span>Sales value</span><strong class="stat-money"><?= marketmate_money($revenue) ?></strong><small>Across recorded sales <span aria-hidden="true">↗</span></small></a>
        <a class="stat-tile" href="<?= site_url('products') ?>"><span>Active products</span><strong><?= number_format((int) $productCount) ?></strong><small><?= (int) $lowStockCount ?> with five or fewer units <span aria-hidden="true">↗</span></small></a>
        <a class="stat-tile" href="<?= site_url('customers') ?>"><span>Customers</span><strong><?= number_format((int) $customerCount) ?></strong><small><?= (int) $userCount ?> active staff accounts <span aria-hidden="true">↗</span></small></a>
    </div>
    <div class="dashboard-body">
        <section class="recent-panel"><div class="panel-heading"><div><p class="eyebrow">ACTIVITY</p><h3>Recent sales</h3></div><a class="text-link" href="<?= site_url('sales') ?>">View history <span aria-hidden="true">→</span></a></div>
            <?php if ($recentSales === []): ?><div class="empty-state"><span class="empty-glyph" aria-hidden="true">↗</span><h4>No sales recorded yet</h4><p>When you make the first sale, it will appear here with the product, staff member, and amount.</p><a class="button primary" href="<?= site_url('sales/new') ?>">Record the first sale</a></div>
            <?php else: ?><div class="recent-list"><?php foreach ($recentSales as $sale): ?><div class="recent-row"><div><strong><?= esc($sale['product_name']) ?></strong><span><?= esc($sale['staff_name']) ?> · <?= esc(date('M j, Y', strtotime($sale['created_at']))) ?></span></div><strong><?= marketmate_money($sale['total_price']) ?></strong></div><?php endforeach; ?></div><?php endif; ?>
        </section>
        <aside class="quick-panel"><p class="eyebrow">QUICK ACCESS</p><h3>Pick up where you need to.</h3><a href="<?= site_url('products') ?>"><span>Manage products</span><span aria-hidden="true">↗</span></a><a href="<?= site_url('customers') ?>"><span>Customer accounts</span><span aria-hidden="true">↗</span></a><a href="<?= site_url('users') ?>"><span>Staff accounts</span><span aria-hidden="true">↗</span></a><a href="<?= site_url('sales/new') ?>"><span>New transaction</span><span aria-hidden="true">↗</span></a></aside>
    </div>
</div></section>
