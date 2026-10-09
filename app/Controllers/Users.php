<?php

namespace App\Controllers;

use App\Libraries\AvatarUploader;
use App\Models\UserModel;
use App\Models\SaleModel;
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
        $formValues = $data;
        $password = $this->request->getPost('password');
        $confirmation = $this->request->getPost('confirm_password');
        $password = is_string($password) ? $password : '';
        $confirmation = is_string($confirmation) ? $confirmation : '';
        $file = $this->request->getFile('avatar');
        $hasUpload = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $rules = $this->rules() + $this->passwordRules();
        if ($hasUpload) {
            $rules['avatar'] = $this->avatarRules();
        }

        if (! $this->validateData(
            $data + ['password' => $password, 'confirm_password' => $confirmation],
            $rules
        )) {
            return redirect()->to(site_url('users/new'))
                ->with('form_values', $formValues)
                ->with('form_errors', $this->validator->getErrors());
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        $uploader = new AvatarUploader();
        $newAvatar = null;
        if ($hasUpload) {
            try {
                $newAvatar = $uploader->store($file);
                $data['avatar'] = $newAvatar;
            } catch (Throwable $exception) {
                log_message('error', 'Avatar upload failed: {message}', ['message' => $exception->getMessage()]);
                return redirect()->to(site_url('users/new'))->with('form_values', $formValues)
                    ->with('form_error', 'The picture could not be prepared. Please try another JPG or PNG.');
            }
        }

        try {
            if ((new UserModel())->insert($data) === false) {
                $uploader->remove($newAvatar);
                return redirect()->to(site_url('users/new'))->with('form_values', $formValues)
                    ->with('form_error', 'The user could not be saved. Please try again.');
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'User insert failed: {message}', ['message' => $exception->getMessage()]);
            $uploader->remove($newAvatar);
            return redirect()->to(site_url('users/new'))->with('form_values', $formValues)
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
        $formValues = $data;
        $password = $this->request->getPost('password');
        $confirmation = $this->request->getPost('confirm_password');
        $password = is_string($password) ? $password : '';
        $confirmation = is_string($confirmation) ? $confirmation : '';
        $file = $this->request->getFile('avatar');
        $hasUpload = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $rules = $this->rules($id);

        if ($password !== '' || $confirmation !== '') {
            $rules += $this->passwordRules();
        }

        if ($hasUpload) {
            $rules['avatar'] = $this->avatarRules();
        }

        if (! $this->validateData($data + ['password' => $password, 'confirm_password' => $confirmation], $rules)) {
            return redirect()->to(site_url("users/{$id}/edit"))
                ->with('form_values', $formValues)
                ->with('form_errors', $this->validator->getErrors());
        }

        if ($user['role'] === 'Admin' && $user['account_status'] === 'Active'
            && ($data['role'] !== 'Admin' || $data['account_status'] !== 'Active')
            && $model->where('role', 'Admin')->where('account_status', 'Active')->countAllResults() <= 1
        ) {
            return redirect()->to(site_url("users/{$id}/edit"))->with('form_values', $formValues)
                ->with('form_error', 'Keep at least one active administrator.');
        }

        if ($password !== '') {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $uploader = new AvatarUploader();
        $newAvatar = null;

        if ($hasUpload) {
            try {
                $newAvatar = $uploader->store($file);
                $data['avatar'] = $newAvatar;
            } catch (Throwable $exception) {
                log_message('error', 'Avatar upload failed: {message}', ['message' => $exception->getMessage()]);

                return redirect()->to(site_url("users/{$id}/edit"))->with('form_values', $formValues)
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

            return redirect()->to(site_url("users/{$id}/edit"))->with('form_values', $formValues)
                ->with('form_error', 'The user could not be updated. Please try again.');
        }

        if ($newAvatar !== null) {
            $uploader->remove($user['avatar'] ?? null);
        }

        return redirect()->to(site_url('users'))->with('notice', 'User updated.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new UserModel();
        $user = $model->find($id);
        if ($user === null) {
            throw PageNotFoundException::forPageNotFound('User not found.');
        }
        if ($id === (int) session('auth_user_id')) {
            return redirect()->to(site_url('users'))->with('form_error', 'You cannot delete your own signed-in account.');
        }
        if ($user['role'] === 'Admin' && $user['account_status'] === 'Active'
            && $model->where('role', 'Admin')->where('account_status', 'Active')->countAllResults() <= 1
        ) {
            return redirect()->to(site_url('users'))->with('form_error', 'Keep at least one active administrator.');
        }
        if ((new SaleModel())->where('sold_by', $id)->countAllResults() > 0) {
            return redirect()->to(site_url('users'))
                ->with('form_error', 'This staff member appears in sales history. Set the account to Inactive instead.');
        }

        try {
            $model->delete($id);
        } catch (DatabaseException $exception) {
            log_message('error', 'User delete failed: {message}', ['message' => $exception->getMessage()]);
            return redirect()->to(site_url('users'))->with('form_error', 'The staff account could not be deleted.');
        }

        (new AvatarUploader())->remove($user['avatar'] ?? null);
        return redirect()->to(site_url('users'))->with('notice', 'Staff account deleted.');
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

    private function passwordRules(): array
    {
        return [
            'password'         => 'required|min_length[12]|max_length[72]',
            'confirm_password' => 'required|matches[password]',
        ];
    }

    private function avatarRules(): string
    {
        return 'uploaded[avatar]|is_image[avatar]|mime_in[avatar,image/jpeg,image/png]'
            . '|ext_in[avatar,jpg,jpeg,png]|max_size[avatar,2048]|max_dims[avatar,4096,4096]';
    }
}
