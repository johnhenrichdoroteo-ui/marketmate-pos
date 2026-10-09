<?php

namespace App\Controllers;

use App\Libraries\ProductImageUploader;
use App\Models\ProductModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

class Products extends BaseController
{
    public function index(): string
    {
        $products = (new ProductModel())->orderBy('is_active', 'DESC')->orderBy('name')->findAll();
        $data = ['title' => 'Products', 'activePage' => 'products', 'products' => $products];

        return view('templates/header', $data) . view('products/index', $data) . view('templates/footer');
    }

    public function new(): string
    {
        $data = ['title' => 'Add product', 'activePage' => 'products', 'product' => null];

        return view('templates/header', $data) . view('products/form', $data) . view('templates/footer');
    }

    public function create(): RedirectResponse
    {
        $data = $this->formData();
        $file = $this->request->getFile('image');
        $hasUpload = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $rules = $this->rules();
        if ($hasUpload) {
            $rules['image'] = $this->imageRules();
        }

        if (! $this->validateData($data, $rules)) {
            return redirect()->to(site_url('products/new'))->withInput();
        }

        $uploader = new ProductImageUploader();
        $newImage = null;
        if ($hasUpload) {
            try {
                $newImage = $uploader->store($file);
                $data['image'] = $newImage;
            } catch (Throwable $exception) {
                log_message('error', 'Product image upload failed: {message}', ['message' => $exception->getMessage()]);
                return redirect()->to(site_url('products/new'))->withInput()
                    ->with('form_error', 'The image could not be prepared. Use a JPG or PNG under 3 MB.');
            }
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        try {
            $saved = (new ProductModel())->insert($data) !== false;
        } catch (DatabaseException $exception) {
            log_message('error', 'Product insert failed: {message}', ['message' => $exception->getMessage()]);
            $saved = false;
        }

        if (! $saved) {
            $uploader->remove($newImage);
            return redirect()->to(site_url('products/new'))->withInput()
                ->with('form_error', 'The product could not be saved. Please try again.');
        }

        return redirect()->to(site_url('products'))->with('notice', 'Product added to the catalog.');
    }

    public function edit(int $id): string
    {
        $product = (new ProductModel())->find($id);
        if ($product === null) {
            throw PageNotFoundException::forPageNotFound('Product not found.');
        }

        $data = ['title' => 'Edit product', 'activePage' => 'products', 'product' => $product];

        return view('templates/header', $data) . view('products/form', $data) . view('templates/footer');
    }

    public function update(int $id): RedirectResponse
    {
        $model = new ProductModel();
        $product = $model->find($id);
        if ($product === null) {
            throw PageNotFoundException::forPageNotFound('Product not found.');
        }

        $data = $this->formData();
        $file = $this->request->getFile('image');
        $hasUpload = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $rules = $this->rules();
        if ($hasUpload) {
            $rules['image'] = $this->imageRules();
        }

        if (! $this->validateData($data, $rules)) {
            return redirect()->to(site_url("products/{$id}/edit"))->withInput();
        }

        $uploader = new ProductImageUploader();
        $newImage = null;
        if ($hasUpload) {
            try {
                $newImage = $uploader->store($file);
                $data['image'] = $newImage;
            } catch (Throwable $exception) {
                log_message('error', 'Product image upload failed: {message}', ['message' => $exception->getMessage()]);
                return redirect()->to(site_url("products/{$id}/edit"))->withInput()
                    ->with('form_error', 'The image could not be prepared. Use a JPG or PNG under 3 MB.');
            }
        }

        try {
            $saved = $model->update($id, $data);
        } catch (DatabaseException $exception) {
            log_message('error', 'Product update failed: {message}', ['message' => $exception->getMessage()]);
            $saved = false;
        }

        if (! $saved) {
            $uploader->remove($newImage);
            return redirect()->to(site_url("products/{$id}/edit"))->withInput()
                ->with('form_error', 'The product could not be updated. Please try again.');
        }

        if ($newImage !== null) {
            $uploader->remove($product['image'] ?? null);
        }

        return redirect()->to(site_url('products'))->with('notice', 'Product updated.');
    }

    public function archive(int $id): RedirectResponse
    {
        return $this->setActive($id, 0, 'Product archived. Its past sales remain in history.');
    }

    public function restore(int $id): RedirectResponse
    {
        return $this->setActive($id, 1, 'Product returned to the catalog.');
    }

    private function setActive(int $id, int $active, string $message): RedirectResponse
    {
        $model = new ProductModel();
        if ($model->find($id) === null) {
            throw PageNotFoundException::forPageNotFound('Product not found.');
        }

        $model->update($id, ['is_active' => $active]);
        return redirect()->to(site_url('products'))->with('notice', $message);
    }

    private function formData(): array
    {
        $fields = ['name', 'price', 'stock_quantity', 'is_active'];
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
            'name'           => 'required|string|min_length[2]|max_length[100]',
            'price'          => 'required|decimal|greater_than[0]|less_than_equal_to[99999999.99]',
            'stock_quantity' => 'required|is_natural|less_than_equal_to[2147483647]',
            'is_active'      => 'required|in_list[0,1]',
        ];
    }

    private function imageRules(): string
    {
        return 'uploaded[image]|is_image[image]|mime_in[image,image/jpeg,image/png]'
            . '|ext_in[image,jpg,jpeg,png]|max_size[image,3072]|max_dims[image,5000,5000]';
    }
}
