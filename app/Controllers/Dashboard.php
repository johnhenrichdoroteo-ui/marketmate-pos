<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $data = [
            'title'         => 'Staff dashboard',
            'activePage'    => 'dashboard',
            'customerCount' => (new CustomerModel())->countAllResults(),
            'userCount'     => (new UserModel())->countAllResults(),
            'displayName'   => (string) session('auth_display_name'),
        ];

        return view('templates/header', $data)
            . view('auth/dashboard', $data)
            . view('templates/footer');
    }
}
