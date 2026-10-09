<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $revenue = $db->table('sales')->selectSum('total_price')->get()->getRowArray();
        $data = [
            'title'          => 'Dashboard',
            'activePage'     => 'dashboard',
            'customerCount'  => (new CustomerModel())->countAllResults(),
            'userCount'      => (new UserModel())->where('account_status', 'Active')->countAllResults(),
            'productCount'   => (new ProductModel())->where('is_active', 1)->countAllResults(),
            'lowStockCount'  => (new ProductModel())->where('is_active', 1)->where('stock_quantity <=', 5)->countAllResults(),
            'saleCount'      => (new SaleModel())->countAllResults(),
            'revenue'        => $revenue['total_price'] ?? '0.00',
            'recentSales'    => (new SaleModel())->withDetails(5),
            'displayName'    => (string) session('auth_display_name'),
        ];

        return view('templates/header', $data)
            . view('auth/dashboard', $data)
            . view('templates/footer');
    }
}
