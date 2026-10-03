<?php
$editing = $customer !== null;
$errors = validation_errors();
$action = $editing ? site_url('customers/' . $customer['id']) : site_url('customers');
?>

<section class="page-banner compact">
    <div class="container narrow">
        <p class="eyebrow">Customer accounts</p>
        <h1><?= $editing ? 'Edit customer' : 'Add a customer' ?></h1>
        <p class="lead">Keep your customer details accurate and easy to find.</p>
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

            <form method="post" action="<?= esc($action, 'attr') ?>">
                <?= csrf_field() ?>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="full_name">Full name <span class="required-mark">*</span></label>
                        <input id="full_name" name="full_name" type="text" maxlength="100" required value="<?= esc(marketmate_old_value('full_name', $customer['full_name'] ?? ''), 'attr') ?>">
                        <?php if (isset($errors['full_name'])): ?><p class="field-error"><?= esc($errors['full_name']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="email">Email address <span class="required-mark">*</span></label>
                        <input id="email" name="email" type="email" maxlength="100" required value="<?= esc(marketmate_old_value('email', $customer['email'] ?? ''), 'attr') ?>">
                        <?php if (isset($errors['email'])): ?><p class="field-error"><?= esc($errors['email']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="phone">Phone number</label>
                        <input id="phone" name="phone" type="tel" maxlength="20" value="<?= esc(marketmate_old_value('phone', $customer['phone'] ?? ''), 'attr') ?>">
                        <?php if (isset($errors['phone'])): ?><p class="field-error"><?= esc($errors['phone']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="address">Address</label>
                        <input id="address" name="address" type="text" maxlength="150" value="<?= esc(marketmate_old_value('address', $customer['address'] ?? ''), 'attr') ?>">
                        <?php if (isset($errors['address'])): ?><p class="field-error"><?= esc($errors['address']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="customer_type">Customer type</label>
                        <select id="customer_type" name="customer_type" required>
                            <?php foreach (['Regular', 'Member'] as $option): ?>
                                <option value="<?= esc($option, 'attr') ?>" <?= marketmate_old_value('customer_type', $customer['customer_type'] ?? 'Regular') === $option ? 'selected' : '' ?>><?= esc($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['customer_type'])): ?><p class="field-error"><?= esc($errors['customer_type']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="loyalty_points">Loyalty points</label>
                        <input id="loyalty_points" name="loyalty_points" type="number" min="0" max="2147483647" step="1" required value="<?= esc(marketmate_old_value('loyalty_points', (string) ($customer['loyalty_points'] ?? 0)), 'attr') ?>">
                        <?php if (isset($errors['loyalty_points'])): ?><p class="field-error"><?= esc($errors['loyalty_points']) ?></p><?php endif; ?>
                    </div>
                    <div class="form-field">
                        <label for="account_status">Account status</label>
                        <select id="account_status" name="account_status" required>
                            <?php foreach (['Active', 'Inactive'] as $option): ?>
                                <option value="<?= esc($option, 'attr') ?>" <?= marketmate_old_value('account_status', $customer['account_status'] ?? 'Active') === $option ? 'selected' : '' ?>><?= esc($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['account_status'])): ?><p class="field-error"><?= esc($errors['account_status']) ?></p><?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="button primary" type="submit"><?= $editing ? 'Save changes' : 'Add customer' ?></button>
                    <a class="button secondary" href="<?= site_url('customers') ?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
