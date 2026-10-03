<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'username', 'full_name', 'email', 'phone', 'password_hash',
        'role', 'account_status', 'last_login', 'created_at', 'avatar',
    ];
}
