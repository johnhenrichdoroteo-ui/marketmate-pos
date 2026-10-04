<?php
$editing = $user !== null;
$errors = session('form_errors') ?? validation_errors();
$action = $editing ? site_url('users/' . $user['id']) : site_url('users');
?>

<section class="page-banner compact">
    <div class="container narrow">
        <p class="eyebrow">User accounts</p>
        <h1><?= $editing ? 'Edit user' : 'Add a user' ?></h1>
        <p class="lead">Keep staff contact details and roles up to date.</p>
    </div>
</section>

<section class="section">
    <div class="container narrow">
        <div class="form-card">
            <?php if ($errors !== []): ?>
                <p class="form-alert" role="alert">Please check the fields below and try again.</p>
            <?php endif; ?>
            <?php if (session('form_error')): ?>
                <p class="form-alert" role="alert"><?= esc(session('form_error')) ?></p>
            <?php endif; ?>

            <form method="post" action="<?= esc($action, 'attr') ?>" <?= $editing ? 'enctype="multipart/form-data"' : '' ?>>
                <?= csrf_field() ?>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="username">Username <span class="required-mark">*</span></label>
                        <input id="username" name="username" type="text" maxlength="50" required value="<?= esc(marketmate_old_value('username', $user['username'] ?? ''), 'attr') ?>">
                        <?php if (isset($errors['username'])): ?><p class="field-error"><?= esc($errors['username']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="full_name">Full name <span class="required-mark">*</span></label>
                        <input id="full_name" name="full_name" type="text" maxlength="100" required value="<?= esc(marketmate_old_value('full_name', $user['full_name'] ?? ''), 'attr') ?>">
                        <?php if (isset($errors['full_name'])): ?><p class="field-error"><?= esc($errors['full_name']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" maxlength="100" value="<?= esc(marketmate_old_value('email', $user['email'] ?? ''), 'attr') ?>">
                        <?php if (isset($errors['email'])): ?><p class="field-error"><?= esc($errors['email']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="phone">Phone number</label>
                        <input id="phone" name="phone" type="tel" maxlength="20" value="<?= esc(marketmate_old_value('phone', $user['phone'] ?? ''), 'attr') ?>">
                        <?php if (isset($errors['phone'])): ?><p class="field-error"><?= esc($errors['phone']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <?php foreach (['Staff', 'Cashier', 'Inventory', 'Supervisor', 'Manager', 'Admin'] as $option): ?>
                                <option value="<?= esc($option, 'attr') ?>" <?= marketmate_old_value('role', $user['role'] ?? 'Staff') === $option ? 'selected' : '' ?>><?= esc($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['role'])): ?><p class="field-error"><?= esc($errors['role']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="account_status">Account status</label>
                        <select id="account_status" name="account_status" required>
                            <?php foreach (['Active', 'Inactive'] as $option): ?>
                                <option value="<?= esc($option, 'attr') ?>" <?= marketmate_old_value('account_status', $user['account_status'] ?? 'Active') === $option ? 'selected' : '' ?>><?= esc($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['account_status'])): ?><p class="field-error"><?= esc($errors['account_status']) ?></p><?php endif; ?>
                    </div>
                </div>

                <div class="password-section">
                    <div class="password-heading">
                        <h2><?= $editing ? 'Change password' : 'Set a password' ?></h2>
                        <p><?= $editing ? 'Leave these fields blank to keep the current password.' : 'The new staff member will use this password to sign in.' ?></p>
                    </div>
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="password"><?= $editing ? 'New password' : 'Password' ?><?= $editing ? '' : ' <span class="required-mark">*</span>' ?></label>
                            <input id="password" name="password" type="password" autocomplete="new-password" minlength="12" maxlength="72" <?= $editing ? '' : 'required' ?>>
                            <p class="field-help">At least 12 characters. Avoid using a shared or easy-to-guess password.</p>
                            <?php if (isset($errors['password'])): ?><p class="field-error"><?= esc($errors['password']) ?></p><?php endif; ?>
                        </div>
                        <div class="form-field">
                            <label for="confirm_password">Confirm password<?= $editing ? '' : ' <span class="required-mark">*</span>' ?></label>
                            <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" <?= $editing ? '' : 'required' ?>>
                            <?php if (isset($errors['confirm_password'])): ?><p class="field-error"><?= esc($errors['confirm_password']) ?></p><?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if ($editing): ?>
                    <div class="avatar-field">
                        <img class="avatar-preview" src="<?= esc(marketmate_avatar_url($user['avatar'] ?? null), 'attr') ?>" alt="Current profile picture for <?= esc($user['full_name'], 'attr') ?>">
                        <div class="form-field">
                            <label for="avatar">Profile picture</label>
                            <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png" aria-describedby="avatar-help">
                            <p class="field-help" id="avatar-help">Optional. JPG or PNG, up to 2 MB. The picture will be cropped to a square.</p>
                            <?php if (isset($errors['avatar'])): ?><p class="field-error"><?= esc($errors['avatar']) ?></p><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <button class="button primary" type="submit"><?= $editing ? 'Save changes' : 'Add user' ?></button>
                    <a class="button secondary" href="<?= site_url('users') ?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
