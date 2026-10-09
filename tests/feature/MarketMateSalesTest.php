<?php

use App\Models\ProductModel;
use App\Models\SaleModel;
use App\Models\UserModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/** @internal */
final class MarketMateSalesTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private BaseConnection $testDb;
    private int $userId;
    private int $productId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testDb = db_connect('tests');
        $this->testDb->query('CREATE TABLE db_customers (
            id INTEGER PRIMARY KEY AUTOINCREMENT, full_name TEXT NOT NULL,
            email TEXT NOT NULL, phone TEXT, address TEXT, customer_type TEXT NOT NULL,
            loyalty_points INTEGER NOT NULL, account_status TEXT NOT NULL, created_at TEXT NOT NULL
        )');
        $this->testDb->query('CREATE TABLE db_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL UNIQUE,
            full_name TEXT NOT NULL, email TEXT, phone TEXT, password_hash TEXT,
            role TEXT NOT NULL, account_status TEXT NOT NULL, last_login TEXT,
            created_at TEXT NOT NULL, avatar TEXT
        )');
        $this->testDb->query('CREATE TABLE db_products (
            id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL,
            price DECIMAL(10,2) NOT NULL, stock_quantity INTEGER NOT NULL,
            image TEXT, is_active INTEGER NOT NULL, created_at TEXT NOT NULL
        )');
        $this->testDb->query('CREATE TABLE db_sales (
            id INTEGER PRIMARY KEY AUTOINCREMENT, product_id INTEGER NOT NULL,
            customer_id INTEGER, sold_by INTEGER NOT NULL, quantity INTEGER NOT NULL,
            total_price DECIMAL(10,2) NOT NULL, created_at TEXT NOT NULL
        )');

        $this->userId = (int) (new UserModel())->insert([
            'username' => 'test_cashier', 'full_name' => 'Test Cashier',
            'password_hash' => password_hash('TestPassword123!', PASSWORD_DEFAULT),
            'role' => 'Cashier', 'account_status' => 'Active',
            'created_at' => '2026-10-06 10:00:00',
        ]);
        $this->productId = (int) (new ProductModel())->insert([
            'name' => 'Coffee beans', 'price' => '125.50', 'stock_quantity' => 5,
            'is_active' => 1, 'created_at' => '2026-10-06 10:00:00',
        ]);
    }

    protected function tearDown(): void
    {
        $this->testDb->query('DROP TABLE db_sales');
        $this->testDb->query('DROP TABLE db_products');
        $this->testDb->query('DROP TABLE db_users');
        $this->testDb->query('DROP TABLE db_customers');
        parent::tearDown();
    }

    public function testSaleUsesServerPriceAndDecreasesStock(): void
    {
        $this->postSale([
            'product_id' => (string) $this->productId,
            'customer_id' => '', 'quantity' => '2', 'total_price' => '0.01',
        ])->assertRedirectTo(site_url('sales'));

        $sale = (new SaleModel())->first();
        $this->assertNotNull($sale);
        $this->assertSame(2, (int) $sale['quantity']);
        $this->assertEquals(251.00, (float) $sale['total_price']);
        $this->assertSame($this->userId, (int) $sale['sold_by']);
        $this->assertNull($sale['customer_id']);
        $this->assertSame(3, (int) (new ProductModel())->find($this->productId)['stock_quantity']);
        $this->withSession($this->staffSession())->get('/sales')->assertOK();
    }

    public function testOversellingLeavesStockAndHistoryUnchanged(): void
    {
        $this->postSale([
            'product_id' => (string) $this->productId,
            'customer_id' => '', 'quantity' => '6',
        ])->assertRedirectTo(site_url('sales/new'));

        $this->assertSame(5, (int) (new ProductModel())->find($this->productId)['stock_quantity']);
        $this->assertSame(0, (new SaleModel())->countAllResults());
    }

    public function testGuestCannotRecordOrSeeSales(): void
    {
        $this->withSession([])->get('/sales')->assertRedirectTo(site_url('login'));
        $this->withSession([])->get('/sales/new')->assertRedirectTo(site_url('login'));
        $this->withSession([])->post('/sales', [
            'product_id' => (string) $this->productId,
            'customer_id' => '', 'quantity' => '1', csrf_token() => csrf_hash(),
        ])->assertRedirectTo(site_url('login'));
        $this->assertSame(0, (new SaleModel())->countAllResults());
    }

    private function staffSession(): array
    {
        return ['auth_user_id' => $this->userId, 'auth_display_name' => 'Test Cashier'];
    }

    private function postSale(array $data)
    {
        $data[csrf_token()] = csrf_hash();
        return $this->withSession($this->staffSession())->post('/sales', $data);
    }
}
