<?php

namespace App\Libraries;

class ProductImageUploader extends PreparedImageUploader
{
    public function __construct()
    {
        parent::__construct('products', 800, 600);
    }
}
