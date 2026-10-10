<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserAvatar extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('avatar', 'users')) {
            $this->forge->addColumn('users', [
                'avatar' => [
                    'type'       => 'MEDIUMTEXT',
                    'null'       => true,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('avatar', 'users')) {
            $this->forge->dropColumn('users', 'avatar');
        }
    }
}
