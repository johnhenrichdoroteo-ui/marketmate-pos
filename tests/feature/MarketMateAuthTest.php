<?php

use App\Models\UserModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/** @internal */
final class MarketMateAuthTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private BaseConnection $testDb;
    private int $userId;

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

        $this->userId = (int) (new UserModel())->insert([
            'username'       => 'test_admin',
            'full_name'      => 'Test Admin',
            'password_hash'  => password_hash('TestPassword123!', PASSWORD_DEFAULT),
            'role'           => 'Admin',
            'account_status' => 'Active',
            'created_at'     => '2026-10-05 10:00:00',
        ]);
    }

    protected function tearDown(): void
    {
        $this->testDb->query('DROP TABLE db_users');
        $this->testDb->query('DROP TABLE db_customers');

        parent::tearDown();
    }

    public function testGuestCannotOpenProtectedPages(): void
    {
        foreach (['/dashboard', '/customers', '/customers/new', '/users', '/users/new', '/users/1/edit'] as $path) {
            $this->withSession([])->get($path)->assertRedirectTo(site_url('login'));
        }
    }

    public function testCorrectPasswordCreatesSessionAndAllowsDashboard(): void
    {
        $result = $this->post('/login', [
            'username'     => 'test_admin',
            'password'     => 'TestPassword123!',
            csrf_token()   => csrf_hash(),
        ]);

        $result->assertRedirectTo(site_url('dashboard'));
        $this->assertSame($this->userId, (int) ($_SESSION['auth_user_id'] ?? 0));

        $this->withSession($_SESSION)->get('/dashboard')->assertOK();
        $this->withSession($_SESSION)->get('/customers')->assertOK();
    }

    public function testWrongPasswordDoesNotCreateLoginSession(): void
    {
        $result = $this->post('/login', [
            'username'     => 'test_admin',
            'password'     => 'incorrect-password',
            csrf_token()   => csrf_hash(),
        ]);

        $result->assertRedirectTo(site_url('login'));
        $this->assertArrayNotHasKey('auth_user_id', $_SESSION);
    }

    public function testInactiveAccountCannotSignIn(): void
    {
        (new UserModel())->update($this->userId, ['account_status' => 'Inactive']);

        $this->post('/login', [
            'username'     => 'test_admin',
            'password'     => 'TestPassword123!',
            csrf_token()   => csrf_hash(),
        ])->assertRedirectTo(site_url('login'));

        $this->assertArrayNotHasKey('auth_user_id', $_SESSION);
    }

    public function testInactiveAccountLosesProtectedAccessEvenWithOldSession(): void
    {
        (new UserModel())->update($this->userId, ['account_status' => 'Inactive']);

        $this->withSession([
            'auth_user_id'      => $this->userId,
            'auth_display_name' => 'Test Admin',
        ])->get('/users')->assertRedirectTo(site_url('login'));

        $this->assertArrayNotHasKey('auth_user_id', $_SESSION);
    }

    public function testLogoutEndsAccessToProtectedPages(): void
    {
        $this->withSession([
            'auth_user_id'      => $this->userId,
            'auth_display_name' => 'Test Admin',
        ])->post('/logout', [csrf_token() => csrf_hash()])->assertRedirectTo(site_url('login'));

        $this->assertArrayNotHasKey('auth_user_id', $_SESSION);
        $this->withSession([])->get('/users')->assertRedirectTo(site_url('login'));
    }
}
