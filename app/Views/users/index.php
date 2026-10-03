<section class="page-banner compact">
    <div class="container">
        <p class="eyebrow">Staff directory</p>
        <h1>User Accounts</h1>
        <p class="lead">Staff records retrieved from the MarketMate POS database.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="table-summary">
            <div>
                <p class="eyebrow">User records</p>
                <h2>Authorized staff</h2>
            </div>
            <div class="table-tools">
                <span class="record-count"><?= count($users) ?> records</span>
                <a class="button primary" href="<?= site_url('users/new') ?>">Add user</a>
            </div>
        </div>

        <?php if (session('notice')): ?>
            <p class="notice" role="status"><?= esc(session('notice')) ?></p>
        <?php endif; ?>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Picture</th>
                        <th scope="col">Username</th>
                        <th scope="col">Full name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Role</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td data-label="Picture"><img class="table-avatar" src="<?= esc(marketmate_avatar_url($user['avatar'] ?? null), 'attr') ?>" alt="Profile picture for <?= esc($user['full_name'], 'attr') ?>"></td>
                            <td data-label="Username"><strong><?= esc($user['username']) ?></strong></td>
                            <td data-label="Full name"><?= esc($user['full_name']) ?></td>
                            <td data-label="Email"><?= esc($user['email'] ?? '') ?></td>
                            <td data-label="Phone"><?= esc($user['phone'] ?? '') ?></td>
                            <td data-label="Role"><span class="role-badge"><?= esc($user['role']) ?></span></td>
                            <td data-label="Status"><span class="status-badge <?= $user['account_status'] === 'Active' ? 'is-active' : 'is-inactive' ?>"><?= esc($user['account_status']) ?></span></td>
                            <td data-label="Action"><a class="table-link" href="<?= site_url('users/' . $user['id'] . '/edit') ?>">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($users === []): ?>
                        <tr><td colspan="8">No users yet. Add the first one to get started.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
