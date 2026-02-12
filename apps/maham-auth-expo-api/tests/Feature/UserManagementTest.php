<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
        $this->superAdmin = $this->createSuperAdmin();
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

        Role::findOrCreate('super-admin', [
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

    protected function createSuperAdmin(): User
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('Password1'),
            'phone' => '0500000001',
            'status' => 'active',
        ]);
        $user->assignRole('super-admin');
        return $user;
    }

    protected function authHeader(?User $user = null): array
    {
        $user = $user ?? $this->superAdmin;
        $token = auth('api')->login($user);
        return ['Authorization' => "Bearer $token"];
    }

    // ==================== LIST USERS ====================

    public function test_admin_can_list_users(): void
    {
        $response = $this->getJson('/api/users', $this->authHeader());

        $response->assertOk()
            ->assertJsonStructure(['success']);
    }

    // ==================== CREATE USER ====================

    public function test_admin_can_create_user(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'new@user.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ], $this->authHeader());

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'new@user.com']);
    }

    // ==================== SHOW USER ====================

    public function test_admin_can_show_user(): void
    {
        $user = User::create([
            'name' => 'View User',
            'email' => 'view@user.com',
            'password' => Hash::make('Password1'),
            'status' => 'active',
        ]);

        $response = $this->getJson("/api/users/{$user->id}", $this->authHeader());

        $response->assertOk();
    }

    // ==================== UPDATE USER ====================

    public function test_admin_can_update_user(): void
    {
        $user = User::create([
            'name' => 'Update Me',
            'email' => 'update@user.com',
            'password' => Hash::make('Password1'),
            'status' => 'active',
        ]);

        $response = $this->putJson("/api/users/{$user->id}", [
            'name' => 'Updated Name',
        ], $this->authHeader());

        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    // ==================== DELETE USER ====================

    public function test_admin_can_delete_user(): void
    {
        $user = User::create([
            'name' => 'Delete Me',
            'email' => 'delete@user.com',
            'password' => Hash::make('Password1'),
            'status' => 'active',
        ]);

        $response = $this->deleteJson("/api/users/{$user->id}", [], $this->authHeader());

        $response->assertOk();
    }

    // ==================== ASSIGN ROLES ====================

    public function test_admin_can_assign_roles_to_user(): void
    {
        $user = User::create([
            'name' => 'Role User',
            'email' => 'role@user.com',
            'password' => Hash::make('Password1'),
            'status' => 'active',
        ]);

        $response = $this->postJson("/api/users/{$user->id}/roles", [
            'roles' => ['admin'],
        ], $this->authHeader());

        $response->assertOk();
        $this->assertTrue($user->fresh()->hasRole('admin'));
    }

    // ==================== ASSIGN PERMISSIONS ====================

    public function test_admin_can_assign_permissions_to_user(): void
    {
        $user = User::create([
            'name' => 'Perm User',
            'email' => 'perm@user.com',
            'password' => Hash::make('Password1'),
            'status' => 'active',
        ]);

        $response = $this->postJson("/api/users/{$user->id}/permissions", [
            'permissions' => ['users.view'],
        ], $this->authHeader());

        $response->assertOk();
    }

    // ==================== GET USER PERMISSIONS ====================

    public function test_admin_can_get_user_permissions(): void
    {
        $user = User::create([
            'name' => 'Perm User',
            'email' => 'getperm@user.com',
            'password' => Hash::make('Password1'),
            'status' => 'active',
        ]);
        $user->assignRole('admin');

        $response = $this->getJson("/api/users/{$user->id}/permissions", $this->authHeader());

        $response->assertOk();
    }

    // ==================== ROLES CRUD ====================

    public function test_admin_can_list_roles(): void
    {
        $response = $this->getJson('/api/roles', $this->authHeader());

        $response->assertOk();
    }

    public function test_admin_can_create_role(): void
    {
        $response = $this->postJson('/api/roles', [
            'name' => 'moderator',
            'display_name' => 'Moderator',
        ], $this->authHeader());

        $response->assertStatus(201);
        $this->assertDatabaseHas('roles', ['name' => 'moderator']);
    }

    public function test_admin_can_show_role(): void
    {
        $role = Role::findOrCreate('admin');

        $response = $this->getJson("/api/roles/{$role->id}", $this->authHeader());

        $response->assertOk();
    }

    public function test_admin_can_update_role(): void
    {
        $role = Role::findOrCreate('test-role', ['display_name' => 'Test Role']);

        $response = $this->putJson("/api/roles/{$role->id}", [
            'display_name' => 'Updated Role',
        ], $this->authHeader());

        $response->assertOk();
    }

    public function test_admin_can_delete_role(): void
    {
        $role = Role::findOrCreate('deletable-role', ['display_name' => 'Deletable']);

        $response = $this->deleteJson("/api/roles/{$role->id}", [], $this->authHeader());

        $response->assertOk();
    }

    // ==================== ROLE PERMISSIONS ====================

    public function test_admin_can_sync_role_permissions(): void
    {
        $role = Role::findOrCreate('test-sync', ['display_name' => 'Test Sync']);

        $response = $this->postJson("/api/roles/{$role->id}/permissions", [
            'permissions' => ['users.view', 'users.create'],
        ], $this->authHeader());

        $response->assertOk();
    }

    public function test_admin_can_add_permissions_to_role(): void
    {
        $role = Role::findOrCreate('test-add', ['display_name' => 'Test Add']);

        $response = $this->postJson("/api/roles/{$role->id}/permissions/add", [
            'permissions' => ['users.view'],
        ], $this->authHeader());

        $response->assertOk();
    }

    public function test_admin_can_remove_permissions_from_role(): void
    {
        $role = Role::findOrCreate('test-remove', ['display_name' => 'Test Remove']);
        $role->givePermissionTo('users.view');

        $response = $this->postJson("/api/roles/{$role->id}/permissions/remove", [
            'permissions' => ['users.view'],
        ], $this->authHeader());

        $response->assertOk();
    }

    // ==================== PERMISSIONS CRUD ====================

    public function test_admin_can_list_permissions(): void
    {
        $response = $this->getJson('/api/permissions', $this->authHeader());

        $response->assertOk();
    }

    public function test_admin_can_create_permission(): void
    {
        $response = $this->postJson('/api/permissions', [
            'name' => 'reports.view',
            'display_name' => 'View Reports',
            'group' => 'reports',
        ], $this->authHeader());

        $response->assertStatus(201);
        $this->assertDatabaseHas('permissions', ['name' => 'reports.view']);
    }

    public function test_admin_can_create_resource_permissions(): void
    {
        $response = $this->postJson('/api/permissions/resource', [
            'resource' => 'reports',
            'actions' => ['view', 'create', 'update', 'delete'],
        ], $this->authHeader());

        $response->assertStatus(201);
    }

    public function test_admin_can_show_permission(): void
    {
        $perm = Permission::findOrCreate('users.view');

        $response = $this->getJson("/api/permissions/{$perm->id}", $this->authHeader());

        $response->assertOk();
    }

    public function test_admin_can_update_permission(): void
    {
        $perm = Permission::findOrCreate('test.perm');

        $response = $this->putJson("/api/permissions/{$perm->id}", [
            'display_name' => 'Updated Permission',
        ], $this->authHeader());

        $response->assertOk();
    }

    public function test_admin_can_delete_permission(): void
    {
        $perm = Permission::findOrCreate('deletable.perm');

        $response = $this->deleteJson("/api/permissions/{$perm->id}", [], $this->authHeader());

        $response->assertOk();
    }
}
