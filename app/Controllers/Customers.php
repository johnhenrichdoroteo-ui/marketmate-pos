<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\SaleModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Customers extends BaseController
{
    public function index(): string
    {
        $customerModel = new CustomerModel();
        $customers = $customerModel->orderBy('id', 'ASC')->findAll();

        $data = [
            'title'      => 'Customer Accounts',
            'activePage' => 'customers',
            'customers'  => $customers,
        ];

        return view('templates/header', $data)
            . view('customers/index', $data)
            . view('templates/footer');
    }

    public function new(): string
    {
        $data = [
            'title'      => 'Add Customer',
            'activePage' => 'customers',
            'customer'   => null,
        ];

        return view('templates/header', $data)
            . view('customers/form', $data)
            . view('templates/footer');
    }

    public function create(): RedirectResponse
    {
        $data = $this->formData();

        if (! $this->validateData($data, $this->rules())) {
            return redirect()->to(site_url('customers/new'))->withInput();
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        try {
            if ((new CustomerModel())->insert($data) === false) {
                return redirect()->to(site_url('customers/new'))->withInput()
                    ->with('form_error', 'The customer could not be saved. Please try again.');
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'Customer insert failed: {message}', ['message' => $exception->getMessage()]);

            return redirect()->to(site_url('customers/new'))->withInput()
                ->with('form_error', 'The customer could not be saved. Please try again.');
        }

        return redirect()->to(site_url('customers'))->with('notice', 'Customer added.');
    }

    public function edit(int $id): string
    {
        $customer = (new CustomerModel())->find($id);

        if ($customer === null) {
            throw PageNotFoundException::forPageNotFound('Customer not found.');
        }

        $data = [
            'title'      => 'Edit Customer',
            'activePage' => 'customers',
            'customer'   => $customer,
        ];

        return view('templates/header', $data)
            . view('customers/form', $data)
            . view('templates/footer');
    }

    public function update(int $id): RedirectResponse
    {
        $model = new CustomerModel();

        if ($model->find($id) === null) {
            throw PageNotFoundException::forPageNotFound('Customer not found.');
        }

        $data = $this->formData();

        if (! $this->validateData($data, $this->rules())) {
            return redirect()->to(site_url("customers/{$id}/edit"))->withInput();
        }

        try {
            if (! $model->update($id, $data)) {
                return redirect()->to(site_url("customers/{$id}/edit"))->withInput()
                    ->with('form_error', 'The customer could not be updated. Please try again.');
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'Customer update failed: {message}', ['message' => $exception->getMessage()]);

            return redirect()->to(site_url("customers/{$id}/edit"))->withInput()
                ->with('form_error', 'The customer could not be updated. Please try again.');
        }

        return redirect()->to(site_url('customers'))->with('notice', 'Customer updated.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new CustomerModel();
        if ($model->find($id) === null) {
            throw PageNotFoundException::forPageNotFound('Customer not found.');
        }

        if ((new SaleModel())->where('customer_id', $id)->countAllResults() > 0) {
            return redirect()->to(site_url('customers'))
                ->with('form_error', 'This customer appears in sales history. Set the account to Inactive instead.');
        }

        try {
            $model->delete($id);
        } catch (DatabaseException $exception) {
            log_message('error', 'Customer delete failed: {message}', ['message' => $exception->getMessage()]);
            return redirect()->to(site_url('customers'))->with('form_error', 'The customer could not be deleted.');
        }

        return redirect()->to(site_url('customers'))->with('notice', 'Customer deleted.');
    }

    private function formData(): array
    {
        $fields = ['full_name', 'email', 'phone', 'address', 'customer_type', 'loyalty_points', 'account_status'];
        $data = [];

        foreach ($fields as $field) {
            $value = $this->request->getPost($field);
            $data[$field] = is_string($value) ? trim($value) : $value;
        }

        return $data;
    }

    private function rules(): array
    {
        return [
            'full_name'      => 'required|string|min_length[2]|max_length[100]',
            'email'          => 'required|valid_email|max_length[100]',
            'phone'          => 'permit_empty|max_length[20]|regex_match[/\A\+?[0-9][0-9 ()-]{5,18}[0-9]\z/]',
            'address'        => 'permit_empty|max_length[150]',
            'customer_type'  => 'required|in_list[Regular,Member]',
            'loyalty_points' => 'required|is_natural|less_than_equal_to[2147483647]',
            'account_status' => 'required|in_list[Active,Inactive]',
        ];
    }
}
