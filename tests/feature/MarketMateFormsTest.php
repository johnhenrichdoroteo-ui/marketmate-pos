<?php

use App\Models\CustomerModel;
use App\Models\UserModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * The in-memory tables keep form tests away from the student's MySQL records.
 * @internal
 */
final class MarketMateFormsTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private BaseConnection $testDb;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testDb = db_connect('tests');
        $this->testDb->query('CREATE TABLE db_customers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT,
            address TEXT,
            customer_type TEXT NOT NULL,
            loyalty_points INTEGER NOT NULL,
            account_status TEXT NOT NULL,
            created_at TEXT NOT NULL
        )');
        $this->testDb->query('CREATE TABLE db_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            full_name TEXT NOT NULL,
            email TEXT,
            phone TEXT,
            password_hash TEXT,
            role TEXT NOT NULL,
            account_status TEXT NOT NULL,
            last_login TEXT,
            created_at TEXT NOT NULL,
            avatar TEXT
        )');
    }

    protected function tearDown(): void
    {
        $this->testDb->query('DROP TABLE db_users');
        $this->testDb->query('DROP TABLE db_customers');

        parent::tearDown();
    }

    public function testCustomerCreateRejectsInvalidInput(): void
    {
        $result = $this->postForm('/customers', [
            'full_name'      => '',
            'email'          => 'not-an-email',
            'phone'          => '',
            'address'        => '',
            'customer_type'  => 'Regular',
            'loyalty_points' => '0',
            'account_status' => 'Active',
        ]);

        $result->assertRedirect();
        $this->assertSame(0, (new CustomerModel())->countAllResults());
    }

    public function testValidFormsCreateRecordsWithoutAcceptingProtectedFields(): void
    {
        $customerResponse = $this->postForm('/customers', [
            'full_name'      => 'New Customer',
            'email'          => 'new@example.test',
            'phone'          => '0917-000-0002',
            'address'        => '',
            'customer_type'  => 'Member',
            'loyalty_points' => '5',
            'account_status' => 'Active',
            'created_at'     => '1900-01-01 00:00:00',
        ]);
        $customerResponse->assertRedirect();

        $customer = (new CustomerModel())->where('email', 'new@example.test')->first();
        $this->assertNotNull($customer);
        $this->assertNotSame('1900-01-01 00:00:00', $customer['created_at']);

        $userResponse = $this->postForm('/users', [
            'username'       => 'new_staff',
            'full_name'      => 'New Staff',
            'email'          => '',
            'phone'          => '',
            'role'           => 'Staff',
            'account_status' => 'Active',
            'password_hash'  => 'posted-hash',
            'avatar'         => 'posted-file.png',
        ]);
        $userResponse->assertRedirect();

        $user = (new UserModel())->where('username', 'new_staff')->first();
        $this->assertNotNull($user);
        $this->assertNull($user['password_hash']);
        $this->assertNull($user['avatar']);
    }

    public function testCustomerEditChangesTheOriginalRecord(): void
    {
        $model = new CustomerModel();
        $id = $model->insert([
            'full_name'      => 'Sample Customer',
            'email'          => 'sample@example.test',
            'phone'          => null,
            'address'        => null,
            'customer_type'  => 'Regular',
            'loyalty_points' => 0,
            'account_status' => 'Active',
            'created_at'     => '2026-10-03 10:00:00',
        ]);

        $result = $this->postForm("/customers/{$id}", [
            'full_name'      => 'Sample Customer',
            'email'          => 'updated@example.test',
            'phone'          => '0917-000-0001',
            'address'        => 'Makati City',
            'customer_type'  => 'Member',
            'loyalty_points' => '12',
            'account_status' => 'Active',
        ]);

        $result->assertRedirect();
        $this->assertSame(1, $model->countAllResults());
        $this->assertSame('updated@example.test', $model->find($id)['email']);
    }

    public function testUserCannotReuseAnotherUsernameButCanKeepTheirOwn(): void
    {
        $model = new UserModel();
        $firstId = $model->insert([
            'username' => 'first_user', 'full_name' => 'First User',
            'role' => 'Staff', 'account_status' => 'Active',
            'created_at' => '2026-10-03 10:00:00',
        ]);
        $secondId = $model->insert([
            'username' => 'second_user', 'full_name' => 'Second User',
            'role' => 'Cashier', 'account_status' => 'Active',
            'created_at' => '2026-10-03 10:00:00',
        ]);

        $duplicate = $this->postForm('/users', [
            'username' => 'first_user', 'full_name' => 'Another User',
            'email' => '', 'phone' => '', 'role' => 'Staff', 'account_status' => 'Active',
        ]);
        $duplicate->assertRedirect();
        $this->assertSame(2, $model->countAllResults());

        $edit = $this->postForm("/users/{$secondId}", [
            'username' => 'second_user', 'full_name' => 'Updated User',
            'email' => '', 'phone' => '', 'role' => 'Manager', 'account_status' => 'Active',
        ]);

        $edit->assertRedirect();
        $this->assertSame('Updated User', $model->find($secondId)['full_name']);
        $this->assertSame(2, $model->countAllResults());
        $this->assertNotSame($firstId, $secondId);
    }

    private function postForm(string $path, array $data)
    {
        $data[csrf_token()] = csrf_hash();

        return $this->post($path, $data);
    }
}
