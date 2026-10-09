<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleModel;
use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

class Sales extends BaseController
{
    public function index(): string
    {
        $sales = (new SaleModel())->withDetails();
        $data = ['title' => 'Sales history', 'activePage' => 'sales', 'sales' => $sales];

        return view('templates/header', $data) . view('sales/index', $data) . view('templates/footer');
    }

    public function new(): string
    {
        $data = [
            'title'      => 'Record sale',
            'activePage' => 'sales',
            'products'   => (new ProductModel())->where('is_active', 1)->where('stock_quantity >', 0)->orderBy('name')->findAll(),
            'customers'  => (new CustomerModel())->where('account_status', 'Active')->orderBy('full_name')->findAll(),
        ];

        return view('templates/header', $data) . view('sales/form', $data) . view('templates/footer');
    }

    public function create(): RedirectResponse
    {
        $raw = [
            'product_id'  => $this->request->getPost('product_id'),
            'customer_id' => $this->request->getPost('customer_id'),
            'quantity'    => $this->request->getPost('quantity'),
        ];
        $rules = [
            'product_id'  => 'required|is_natural_no_zero',
            'customer_id' => 'permit_empty|is_natural_no_zero',
            'quantity'    => 'required|is_natural_no_zero|less_than_equal_to[100000]',
        ];

        if (! $this->validateData($raw, $rules)) {
            return redirect()->to(site_url('sales/new'))->withInput();
        }

        $productId = (int) $raw['product_id'];
        $customerId = $raw['customer_id'] === '' || $raw['customer_id'] === null ? null : (int) $raw['customer_id'];
        $quantity = (int) $raw['quantity'];
        $product = (new ProductModel())->find($productId);

        if ($product === null || (int) $product['is_active'] !== 1) {
            return $this->saleError('Choose an available product.');
        }
        if ($customerId !== null) {
            $customer = (new CustomerModel())->find($customerId);
            if ($customer === null || $customer['account_status'] !== 'Active') {
                return $this->saleError('Choose an active customer or leave the customer blank.');
            }
        }
        if ($quantity > (int) $product['stock_quantity']) {
            return $this->saleError('There is not enough stock for that quantity.');
        }

        // The server calculates the total from its own product price, never from a posted amount.
        [$whole, $fraction] = array_pad(explode('.', (string) $product['price'], 2), 2, '00');
        $unitCents = ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0');
        $totalCents = $unitCents * $quantity;
        if ($totalCents > 9999999999) {
            return $this->saleError('This sale exceeds the maximum amount. Reduce the quantity.');
        }
        $totalPrice = sprintf('%d.%02d', intdiv($totalCents, 100), $totalCents % 100);

        $db = db_connect();
        $db->transBegin();
        try {
            // This conditional update is atomic: two cashiers cannot sell the same last unit.
            $db->table('products')
                ->set('stock_quantity', 'stock_quantity - ' . $quantity, false)
                ->where('id', $productId)
                ->where('is_active', 1)
                ->where('stock_quantity >=', $quantity)
                ->update();

            if ($db->affectedRows() !== 1) {
                $db->transRollback();
                return $this->saleError('Stock changed while you were checking out. Review the available quantity.');
            }

            $saleId = (new SaleModel())->insert([
                'product_id'  => $productId,
                'customer_id' => $customerId,
                'sold_by'     => (int) session('auth_user_id'),
                'quantity'    => $quantity,
                'total_price' => $totalPrice,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);

            if ($saleId === false || ! $db->transStatus()) {
                $db->transRollback();
                return $this->saleError('The sale could not be recorded. No stock was changed.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();
            log_message('error', 'Sale transaction failed: {message}', ['message' => $exception->getMessage()]);
            return $this->saleError('The sale could not be recorded. No stock was changed.');
        }

        return redirect()->to(site_url('sales'))->with('notice', 'Sale recorded and stock updated.');
    }

    private function saleError(string $message): RedirectResponse
    {
        return redirect()->to(site_url('sales/new'))->withInput()->with('form_error', $message);
    }
}
