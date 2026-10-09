<?php

namespace App\Libraries;

class AvatarUploader extends PreparedImageUploader
{
    public function __construct()
    {
        parent::__construct('avatars', 300, 300);
    }
}
