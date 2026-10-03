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
        $value = old($field, $default, false);

        return is_scalar($value) ? (string) $value : '';
    }
}
