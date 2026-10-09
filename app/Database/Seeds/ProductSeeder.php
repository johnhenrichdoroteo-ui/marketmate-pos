<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'Wireless Mouse', 'price' => '649.00', 'stock_quantity' => 24, 'image' => 'wireless-mouse.jpg'],
            ['name' => 'USB-C Charging Cable', 'price' => '299.00', 'stock_quantity' => 36, 'image' => 'usb-c-charging-cable.jpg'],
            ['name' => '65W GaN Wall Charger', 'price' => '1399.00', 'stock_quantity' => 18, 'image' => '65w-gan-wall-charger.jpg'],
            ['name' => '10,000mAh Power Bank', 'price' => '1599.00', 'stock_quantity' => 13, 'image' => '10000mah-power-bank.jpg'],
            ['name' => 'Wireless Earbuds', 'price' => '2199.00', 'stock_quantity' => 15, 'image' => 'wireless-earbuds.jpg'],
            ['name' => 'Compact Mechanical Keyboard', 'price' => '2699.00', 'stock_quantity' => 9, 'image' => 'compact-mechanical-keyboard-photo.jpg'],
            ['name' => 'Full HD USB Webcam', 'price' => '1799.00', 'stock_quantity' => 11, 'image' => 'full-hd-usb-webcam.jpg'],
            ['name' => 'Foldable Phone Stand', 'price' => '225.00', 'stock_quantity' => 12, 'image' => 'foldable-phone-stand.jpg'],
            ['name' => 'Mini Bluetooth Speaker', 'price' => '1299.00', 'stock_quantity' => 8, 'image' => 'mini-bluetooth-speaker.jpg'],
            ['name' => 'Portable SSD 512GB', 'price' => '3299.00', 'stock_quantity' => 7, 'image' => 'portable-ssd-512gb.jpg'],
            ['name' => '7-Port USB Hub', 'price' => '899.00', 'stock_quantity' => 16, 'image' => '7-port-usb-hub.jpg'],
            ['name' => 'Over-Ear Wireless Headphones', 'price' => '3499.00', 'stock_quantity' => 10, 'image' => 'over-ear-wireless-headphones.jpg'],
            ['name' => 'Laptop Sleeve 14-inch', 'price' => '749.00', 'stock_quantity' => 20, 'image' => 'laptop-sleeve-14-inch.jpg'],
            ['name' => 'Portable Monitor 15.6-inch', 'price' => '7999.00', 'stock_quantity' => 5, 'image' => 'portable-monitor-15-6-inch.jpg'],
            ['name' => 'USB-C Docking Station', 'price' => '3999.00', 'stock_quantity' => 8, 'image' => 'usb-c-docking-station.jpg'],
            ['name' => 'Smart Desk Lamp', 'price' => '1899.00', 'stock_quantity' => 12, 'image' => 'smart-desk-lamp.jpg'],
            ['name' => 'Wireless Presenter', 'price' => '999.00', 'stock_quantity' => 14, 'image' => 'wireless-presenter.jpg'],
            ['name' => '4K HDMI Cable', 'price' => '399.00', 'stock_quantity' => 30, 'image' => '4k-hdmi-cable.jpg'],
            ['name' => '2TB External Hard Drive', 'price' => '4599.00', 'stock_quantity' => 6, 'image' => '2tb-external-hard-drive.jpg'],
            ['name' => 'Rechargeable Desk Fan', 'price' => '1199.00', 'stock_quantity' => 15, 'image' => 'rechargeable-desk-fan.jpg'],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($products as $product) {
            $existing = $this->db->table('products')
                ->where('name', $product['name'])
                ->get()
                ->getRowArray();

            if ($existing === null) {
                // Add only missing samples; never reset prices or stock already entered by staff.
                $this->db->table('products')->insert($product + [
                    'is_active' => 1,
                    'created_at' => $now,
                ]);
                continue;
            }

            if (($existing['image'] === null || $existing['image'] === '') && $product['image'] !== null) {
                $this->db->table('products')
                    ->where('id', $existing['id'])
                    ->update(['image' => $product['image']]);
            }
        }
    }
}
