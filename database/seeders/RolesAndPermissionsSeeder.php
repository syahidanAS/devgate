<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Articles
            'article.create',
            'article.view',
            'article.edit',
            'article.delete',
            'article.publish',
            // Products
            'product.create',
            'product.view',
            'product.edit',
            'product.delete',
            // Orders & Payments
            'order.view',
            'order.edit',
            'payment.view',
            // Users
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            // Media
            'media.upload',
            // Admin Panel Access
            'admin.access',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles and Assign Permissions
        $authorRole = Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web']);
        $authorRole->syncPermissions([
            'article.create',
            'article.view',
            'article.edit',
            'article.delete',
            'media.upload',
            'admin.access',
        ]);

        $marketplaceAdminRole = Role::firstOrCreate(['name' => 'admin-marketplace', 'guard_name' => 'web']);
        $marketplaceAdminRole->syncPermissions([
            'product.create',
            'product.view',
            'product.edit',
            'product.delete',
            'order.view',
            'order.edit',
            'payment.view',
            'admin.access',
        ]);

        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        // Superadmin automatically gets all permissions via a gate callback in AuthServiceProvider

        // Clear permissions cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Demo Users
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@devgate.id'],
            [
                'name' => 'Super Admin DevGate',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        $author = User::updateOrCreate(
            ['email' => 'author@devgate.id'],
            [
                'name' => 'IoT Specialist Author',
                'username' => 'iot_author',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'bio' => 'IoT Specialist with 5+ years of experience in hardware programming and RTOS.',
                'social_links' => ['github' => 'https://github.com', 'twitter' => 'https://twitter.com'],
            ]
        );
        $author->assignRole($authorRole);

        $marketplaceAdmin = User::updateOrCreate(
            ['email' => 'admin@devgate.id'],
            [
                'name' => 'Marketplace Manager',
                'username' => 'shop_admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $marketplaceAdmin->assignRole($marketplaceAdminRole);
    }
}
