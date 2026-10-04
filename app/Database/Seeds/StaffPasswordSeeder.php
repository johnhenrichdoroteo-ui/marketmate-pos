<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class StaffPasswordSeeder extends Seeder
{
    public function run(): void
    {
        $users = $this->db->table('users')
            ->select('id, password_hash')
            ->get()
            ->getResultArray();
        $count = 0;

        foreach ($users as $user) {
            if (! empty($user['password_hash'])) {
                continue;
            }

            // Each existing account gets its own unknown, random password.
            // An administrator can set a usable one through the private CLI command.
            $temporaryPassword = bin2hex(random_bytes(18));
            $this->db->table('users')
                ->where('id', $user['id'])
                ->update(['password_hash' => password_hash($temporaryPassword, PASSWORD_DEFAULT)]);
            $count++;
        }

        CLI::write("Added password hashes to {$count} existing staff accounts.");
        CLI::write('Run php spark marketmate:password admin01 to create a one-time sign-in password.');
    }
}
