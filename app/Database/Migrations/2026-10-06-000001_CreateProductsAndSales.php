<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsAndSales extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('products')) {
            $this->forge->addField([
                'id'             => ['type' => 'INT', 'auto_increment' => true],
                'name'           => ['type' => 'VARCHAR', 'constraint' => 100],
                'price'          => ['type' => 'DECIMAL', 'constraint' => '10,2'],
                'stock_quantity' => ['type' => 'INT', 'default' => 0],
                'image'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'is_active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at'     => ['type' => 'DATETIME'],
            ]);
            $this->forge->addPrimaryKey('id');
            $this->forge->createTable('products');
        }

        if (! $this->db->tableExists('sales')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'auto_increment' => true],
                'product_id'  => ['type' => 'INT'],
                'customer_id' => ['type' => 'INT', 'null' => true],
                'sold_by'     => ['type' => 'INT'],
                'quantity'    => ['type' => 'INT'],
                'total_price' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
                'created_at'  => ['type' => 'DATETIME'],
            ]);
            $this->forge->addPrimaryKey('id');
            $this->forge->addKey('created_at');
            $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'RESTRICT');
            $this->forge->addForeignKey('customer_id', 'customers', 'id', 'RESTRICT', 'RESTRICT');
            $this->forge->addForeignKey('sold_by', 'users', 'id', 'RESTRICT', 'RESTRICT');
            $this->forge->createTable('sales');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('sales')) {
            $this->forge->dropTable('sales');
        }
        if ($this->db->tableExists('products')) {
            $this->forge->dropTable('products');
        }
    }
}
