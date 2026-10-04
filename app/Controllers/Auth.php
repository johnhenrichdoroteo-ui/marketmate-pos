<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    public function login(): string
    {
        return view('auth/login', ['title' => 'Staff sign in']);
    }

    public function attempt(): RedirectResponse
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $username = is_string($username) ? trim($username) : '';
        $password = is_string($password) ? $password : '';

        if (! $this->validateData(
            ['username' => $username, 'password' => $password],
            ['username' => 'required|max_length[50]', 'password' => 'required|max_length[255]']
        )) {
            return redirect()->to(site_url('login'))
                ->with('auth_error', 'Enter your username and password.')
                ->with('login_username', $username);
        }

        $model = new UserModel();
        $user = $model->where('username', $username)->first();

        if ($user === null
            || $user['account_status'] !== 'Active'
            || empty($user['password_hash'])
            || ! password_verify($password, $user['password_hash'])
        ) {
            return redirect()->to(site_url('login'))
                ->with('auth_error', 'The username or password is incorrect.')
                ->with('login_username', $username);
        }

        $session = session();
        $session->regenerate(true);
        $session->set([
            'auth_user_id'      => (int) $user['id'],
            'auth_display_name' => $user['full_name'],
        ]);

        $model->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        return redirect()->to(site_url('dashboard'));
    }

    public function logout(): RedirectResponse
    {
        $session = session();
        $session->remove(['auth_user_id', 'auth_display_name']);
        $session->destroy();

        return redirect()->to(site_url('login'));
    }
}
