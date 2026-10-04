<?php

namespace App\Commands;

use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SetStaffPassword extends BaseCommand
{
    protected $group = 'MarketMate';
    protected $name = 'marketmate:password';
    protected $description = 'Generate a new, one-time password for an existing staff account.';
    protected $usage = 'marketmate:password <username>';
    protected $arguments = [
        'username' => 'The staff username whose password will be reset.',
    ];

    public function run(array $params)
    {
        $username = $params[0] ?? '';

        if ($username === '') {
            CLI::error('Provide a username, for example: php spark marketmate:password admin01');

            return EXIT_ERROR;
        }

        $model = new UserModel();
        $user = $model->where('username', $username)->first();

        if ($user === null) {
            CLI::error('That staff account was not found.');

            return EXIT_ERROR;
        }

        $temporaryPassword = bin2hex(random_bytes(18));
        $model->update($user['id'], [
            'password_hash' => password_hash($temporaryPassword, PASSWORD_DEFAULT),
        ]);

        CLI::write('Username: ' . $username);
        CLI::write('One-time password: ' . $temporaryPassword);
        CLI::write('Copy it now; it is not stored in plaintext and cannot be shown again.');
        CLI::write('Do not commit or publish this password.');

        return EXIT_SUCCESS;
    }
}
