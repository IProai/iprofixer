<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class CmsPermissionSeeder extends Seeder
{
    /** @var list<string> */
    public const PERMISSIONS = [
        'content.create',
        'content.edit',
        'content.review',
        'content.approve',
        'content.publish',
        'content.preview',
        'content.restore',
        'content.archive',
        'content.manage',
    ];

    /** @var array<string, list<string>> */
    public const ROLES = [
        'Content Editor' => [
            'content.create',
            'content.edit',
            'content.preview',
        ],
        'Content Reviewer' => [
            'content.create',
            'content.edit',
            'content.review',
            'content.preview',
        ],
        'Content Publisher' => [
            'content.create',
            'content.edit',
            'content.review',
            'content.approve',
            'content.publish',
            'content.preview',
            'content.restore',
            'content.archive',
        ],
        'Content Administrator' => self::PERMISSIONS,
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        foreach (self::ROLES as $roleName => $permissions) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['is_system' => true],
            );

            $role->syncPermissions($permissions);
        }
    }
}
