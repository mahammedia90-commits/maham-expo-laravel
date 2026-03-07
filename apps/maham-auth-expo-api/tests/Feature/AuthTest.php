<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    protected function seedRolesAndPermissions(): void
    {
        $permissions = [
            'users.view', 'users.create', 'users.update', 'users.delete',
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.update', 'permissions.delete',
            'services.view', 'services.create', 'services.update', 'services.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, ['is_system' => true]);
        }

        $superAdmin = Role::findOrCreate('super-admin', [
            'description' => 'Full system access',
            'is_system' => true,
            'level' => 100,
        ]);

        $admin = Role::findOrCreate('admin', [
            'description' => 'Administrative access',
            'is_system' => true,
            'level' => 50,
        ]);
        $admin->syncPermissions(['users.view', 'users.create', 'users.update', 'roles.view', 'permissions.view']);

        Role::findOrCreate('user', [
            'description' => 'Regular user access',
            'is_system' => true,
            'level' => 10,
        ]);
    }

    protected function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('Password1'),
            'phone' => '0500000000',
            'status' => 'active',
        ], $overrides));
    }

    protected function createSuperAdmin(): User
    {
        $user = $this->createUser([
            'email' => 'super@admin.com',
            'phone' => '0500000001',
        ]);
        $user->assignRole('super-admin');
        return $user;
    }

    protected function createAdmin(): User
    {
        $user = $this->createUser([
            'email' => 'admin@test.com',
            'phone' => '0500000002',
        ]);
        $user->assignRole('admin');
        return $user;
    }

    protected function authHeader(User $user): array
    {
        $token = auth('api')->login($user);
        return ['Authorization' => "Bearer $token"];
    }

    // ==================== REGISTER ====================

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'New User',
            'email' => 'new@user.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'phone' => '0500000099',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['user', 'token'],
            ]);

        $this->assertDatabaseHas('users', ['email' => 'new@user.com']);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        $this->createUser(['email' => 'existing@test.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'New User',
            'email' => 'existing@test.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'phone' => '0500000099',
        ]);

        $response->assertStatus(400);
    }

    public function test_register_fails_with_weak_password(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'New User',
            'email' => 'new@user.com',
            'password' => '123',
            'password_confirmation' => '123',
            'phone' => '0500000099',
        ]);

        $response->assertStatus(400);
    }

    // ==================== LOGIN ====================

    public function test_user_can_login_with_email(): void
    {
        $this->createUser(['email' => 'login@test.com', 'password' => Hash::make('Password1')]);

        $response = $this->postJson('/api/auth/login', [
            'identifier' => 'login@test.com',
            'password' => 'Password1',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['user', 'token'],
            ]);
    }

    public function test_user_can_login_with_phone(): void
    {
        $this->createUser(['phone' => '0500001111', 'password' => Hash::make('Password1')]);

        $response = $this->postJson('/api/auth/login', [
            'identifier' => '0500001111',
            'password' => 'Password1',
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->createUser(['email' => 'login@test.com']);

        $response = $this->postJson('/api/auth/login', [
            'identifier' => 'login@test.com',
            'password' => 'WrongPass1',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_login_fails_for_inactive_user(): void
    {
        $this->createUser(['email' => 'inactive@test.com', 'status' => 'suspended']);

        $response = $this->postJson('/api/auth/login', [
            'identifier' => 'inactive@test.com',
            'password' => 'Password1',
        ]);

        $response->assertStatus(401);
    }

    // ==================== LOGOUT ====================

    public function test_user_can_logout(): void
    {
        $user = $this->createUser();

        $response = $this->postJson('/api/auth/logout', [], $this->authHeader($user));

        $response->assertOk()
            ->assertJson(['success' => true]);
    }

    // ==================== REFRESH TOKEN ====================

    public function test_user_can_refresh_token(): void
    {
        $user = $this->createUser();

        $response = $this->postJson('/api/auth/refresh', [], $this->authHeader($user));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['token'],
            ]);
    }

    // ==================== ME ====================

    public function test_user_can_get_profile(): void
    {
        $user = $this->createUser();

        $response = $this->getJson('/api/auth/me', $this->authHeader($user));

        $response->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_me_fails_without_token(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    // ==================== CHANGE PASSWORD ====================

    public function test_user_can_change_password(): void
    {
        $user = $this->createUser(['password' => Hash::make('Password1')]);

        $response = $this->postJson('/api/auth/change-password', [
            'current_password' => 'Password1',
            'password' => 'NewPassword1',
            'password_confirmation' => 'NewPassword1',
        ], $this->authHeader($user));

        $response->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_change_password_fails_with_wrong_current(): void
    {
        $user = $this->createUser(['password' => Hash::make('Password1')]);

        $response = $this->postJson('/api/auth/change-password', [
            'current_password' => 'WrongPass1',
            'password' => 'NewPassword1',
            'password_confirmation' => 'NewPassword1',
        ], $this->authHeader($user));

        $response->assertStatus(400);
    }

    // ==================== UPDATE PROFILE ====================

    public function test_user_can_update_profile(): void
    {
        $user = $this->createUser();

        $response = $this->putJson('/api/auth/profile', [
            'name' => 'Updated Name',
        ], $this->authHeader($user));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    // ==================== FORGOT / RESET PASSWORD ====================

    public function test_forgot_password_sends_reset_token(): void
    {
        $this->createUser(['email' => 'reset@test.com']);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'reset@test.com',
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_forgot_password_fails_for_unknown_email(): void
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'unknown@test.com',
        ]);

        $response->assertStatus(400);
    }

    // ==================== EMAIL VERIFICATION ====================

    public function test_user_can_send_email_verification(): void
    {
        $user = $this->createUser(['email_verified_at' => null]);

        $response = $this->postJson('/api/auth/email/send-verification', [], $this->authHeader($user));

        $response->assertOk()
            ->assertJson(['success' => true]);
    }

    // ==================== VERIFY TOKEN (INTERNAL) ====================

    public function test_verify_token_returns_user_data(): void
    {
        $user = $this->createUser();
        $token = auth('api')->login($user);

        $response = $this->postJson('/api/verify-token', [
            'token' => $token,
        ], $this->authHeader($user));

        $response->assertOk()
            ->assertJsonPath('data.valid', true);
    }

    // ==================== CHECK PERMISSION ====================

    public function test_check_permission_for_user(): void
    {
        $user = $this->createAdmin();

        $response = $this->postJson('/api/check-permission', [
            'user_id' => $user->id,
            'permission' => 'users.view',
        ], $this->authHeader($user));

        $response->assertOk()
            ->assertJsonPath('data.has_permission', true);
    }

    // ==================== CHECK PERMISSIONS (BULK) ====================

    public function test_check_permissions_bulk(): void
    {
        $user = $this->createAdmin();

        $response = $this->postJson('/api/check-permissions', [
            'user_id' => $user->id,
            'permissions' => ['users.view', 'users.delete'],
            'require_all' => false,
        ], $this->authHeader($user));

        $response->assertOk()
            ->assertJsonPath('data.has_access', true);
    }
}
