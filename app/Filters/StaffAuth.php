<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class StaffAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userId = session()->get('auth_user_id');
        $user = is_numeric($userId) ? (new UserModel())->find((int) $userId) : null;

        if ($user !== null && $user['account_status'] === 'Active' && ! empty($user['password_hash'])) {
            return null;
        }

        session()->remove(['auth_user_id', 'auth_display_name']);

        return redirect()->to(site_url('login'))->with('auth_notice', 'Please sign in to access the staff workspace.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('Cache-Control', 'no-store, max-age=0');
        $response->setHeader('Pragma', 'no-cache');
    }
}
