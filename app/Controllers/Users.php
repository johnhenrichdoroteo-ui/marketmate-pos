<?php

namespace App\Controllers;

use App\Libraries\AvatarUploader;
use App\Models\UserModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

class Users extends BaseController
{
    public function index(): string
    {
        $userModel = new UserModel();
        $users = $userModel->orderBy('id', 'ASC')->findAll();

        $data = [
            'title'      => 'User Accounts',
            'activePage' => 'users',
            'users'      => $users,
        ];

        return view('templates/header', $data)
            . view('users/index', $data)
            . view('templates/footer');
    }

    public function new(): string
    {
        $data = [
            'title'      => 'Add User',
            'activePage' => 'users',
            'user'       => null,
        ];

        return view('templates/header', $data)
            . view('users/form', $data)
            . view('templates/footer');
    }

    public function create(): RedirectResponse
    {
        $data = $this->formData();

        if (! $this->validateData($data, $this->rules())) {
            return redirect()->to(site_url('users/new'))->withInput();
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        try {
            if ((new UserModel())->insert($data) === false) {
                return redirect()->to(site_url('users/new'))->withInput()
                    ->with('form_error', 'The user could not be saved. Please try again.');
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'User insert failed: {message}', ['message' => $exception->getMessage()]);

            return redirect()->to(site_url('users/new'))->withInput()
                ->with('form_error', 'The user could not be saved. Please try again.');
        }

        return redirect()->to(site_url('users'))->with('notice', 'User added.');
    }

    public function edit(int $id): string
    {
        $user = (new UserModel())->find($id);

        if ($user === null) {
            throw PageNotFoundException::forPageNotFound('User not found.');
        }

        $data = [
            'title'      => 'Edit User',
            'activePage' => 'users',
            'user'       => $user,
        ];

        return view('templates/header', $data)
            . view('users/form', $data)
            . view('templates/footer');
    }

    public function update(int $id): RedirectResponse
    {
        $model = new UserModel();
        $user = $model->find($id);

        if ($user === null) {
            throw PageNotFoundException::forPageNotFound('User not found.');
        }

        $data = $this->formData();
        $file = $this->request->getFile('avatar');
        $hasUpload = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $rules = $this->rules($id);

        if ($hasUpload) {
            $rules['avatar'] = 'uploaded[avatar]|is_image[avatar]|mime_in[avatar,image/jpeg,image/png]'
                . '|ext_in[avatar,jpg,jpeg,png]|max_size[avatar,2048]|max_dims[avatar,4096,4096]';
        }

        if (! $this->validateData($data, $rules)) {
            return redirect()->to(site_url("users/{$id}/edit"))->withInput();
        }

        $uploader = new AvatarUploader();
        $newAvatar = null;

        if ($hasUpload) {
            try {
                $newAvatar = $uploader->store($file);
                $data['avatar'] = $newAvatar;
            } catch (Throwable $exception) {
                log_message('error', 'Avatar upload failed: {message}', ['message' => $exception->getMessage()]);

                return redirect()->to(site_url("users/{$id}/edit"))->withInput()
                    ->with('form_error', 'The picture could not be prepared. Please try another JPG or PNG.');
            }
        }

        try {
            $saved = $model->update($id, $data);
        } catch (DatabaseException $exception) {
            log_message('error', 'User update failed: {message}', ['message' => $exception->getMessage()]);
            $saved = false;
        }

        if (! $saved) {
            $uploader->remove($newAvatar);

            return redirect()->to(site_url("users/{$id}/edit"))->withInput()
                ->with('form_error', 'The user could not be updated. Please try again.');
        }

        if ($newAvatar !== null) {
            $uploader->remove($user['avatar'] ?? null);
        }

        return redirect()->to(site_url('users'))->with('notice', 'User updated.');
    }

    private function formData(): array
    {
        $fields = ['username', 'full_name', 'email', 'phone', 'role', 'account_status'];
        $data = [];

        foreach ($fields as $field) {
            $value = $this->request->getPost($field);
            $data[$field] = is_string($value) ? trim($value) : $value;
        }

        return $data;
    }

    private function rules(?int $id = null): array
    {
        $uniqueUsername = $id === null
            ? 'is_unique[users.username]'
            : "is_unique[users.username,id,{$id}]";

        return [
            'username'       => "required|alpha_dash|max_length[50]|{$uniqueUsername}",
            'full_name'      => 'required|string|min_length[2]|max_length[100]',
            'email'          => 'permit_empty|valid_email|max_length[100]',
            'phone'          => 'permit_empty|max_length[20]|regex_match[/\A\+?[0-9][0-9 ()-]{5,18}[0-9]\z/]',
            'role'           => 'required|in_list[Admin,Manager,Cashier,Inventory,Supervisor,Staff]',
            'account_status' => 'required|in_list[Active,Inactive]',
        ];
    }
}
