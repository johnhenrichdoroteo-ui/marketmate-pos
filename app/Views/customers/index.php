<section class="page-banner compact">
    <div class="container">
        <p class="eyebrow">Account directory</p>
        <h1>Customer Accounts</h1>
        <p class="lead">Customer contact records retrieved from the MarketMate POS database.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="table-summary">
            <div>
                <p class="eyebrow">Customer records</p>
                <h2>Registered customers</h2>
            </div>
            <div class="table-tools">
                <span class="record-count"><?= count($customers) ?> records</span>
                <a class="button primary" href="<?= site_url('customers/new') ?>">Add customer</a>
            </div>
        </div>

        <?php if (session('notice')): ?>
            <p class="notice" role="status"><?= esc(session('notice')) ?></p>
        <?php endif; ?>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Full name</th>
                        <th scope="col">Email address</th>
                        <th scope="col">Phone number</th>
                        <th scope="col">Type</th>
                        <th scope="col">Points</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td data-label="Full name">
                                <strong><?= esc($customer['full_name']) ?></strong>
                                <?php if (! empty($customer['address'])): ?>
                                    <span class="table-subtext"><?= esc($customer['address']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Email"><?= esc($customer['email']) ?></td>
                            <td data-label="Phone"><?= esc($customer['phone'] ?? '') ?></td>
                            <td data-label="Type"><span class="role-badge"><?= esc($customer['customer_type']) ?></span></td>
                            <td data-label="Points"><?= esc($customer['loyalty_points']) ?></td>
                            <td data-label="Status"><span class="status-badge <?= $customer['account_status'] === 'Active' ? 'is-active' : 'is-inactive' ?>"><?= esc($customer['account_status']) ?></span></td>
                            <td data-label="Action"><a class="table-link" href="<?= site_url('customers/' . $customer['id'] . '/edit') ?>">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($customers === []): ?>
                        <tr><td colspan="7">No customers yet. Add the first one to get started.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
