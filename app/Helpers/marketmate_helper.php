<?php

if (! function_exists('marketmate_avatar_url')) {
    function marketmate_avatar_url(?string $filename): string
    {
        if ($filename !== null
            && preg_match('/\A[a-f0-9]{32}\.(?:jpg|png)\z/', $filename) === 1
            && is_file(FCPATH . 'uploads/avatars/' . $filename)
        ) {
            return base_url('uploads/avatars/' . $filename);
        }

        return base_url('assets/images/avatar-placeholder.svg');
    }
}

if (! function_exists('marketmate_old_value')) {
    function marketmate_old_value(string $field, string $default = ''): string
    {
        $formValues = session('form_values');

        if (is_array($formValues) && array_key_exists($field, $formValues)) {
            $value = $formValues[$field];

            return is_scalar($value) ? (string) $value : '';
        }

        $value = old($field, $default, false);

        return is_scalar($value) ? (string) $value : '';
    }
}

if (! function_exists('marketmate_product_image_url')) {
    function marketmate_product_image_url(?string $filename): string
    {
        if ($filename !== null
            && preg_match('/\A[a-f0-9]{32}\.(?:jpg|png)\z/', $filename) === 1
            && is_file(FCPATH . 'uploads/products/' . $filename)
        ) {
            return base_url('uploads/products/' . $filename);
        }

        if ($filename !== null
            && preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\.jpg\z/', $filename) === 1
            && is_file(FCPATH . 'assets/images/products/' . $filename)
        ) {
            return base_url('assets/images/products/' . $filename);
        }

        return base_url('assets/images/product-placeholder.svg');
    }
}

if (! function_exists('marketmate_money')) {
    function marketmate_money(int|float|string $amount): string
    {
        return '₱' . number_format((float) $amount, 2);
    }
}
