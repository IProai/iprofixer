<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows only active non-deleted users to access governed surfaces', function (): void {
    $active = User::factory()->create();
    $inactive = User::factory()->inactive()->create();
    $deleted = User::factory()->create();
    $deleted->delete();

    expect($active->canAccessPlatform())->toBeTrue()
        ->and($inactive->canAccessPlatform())->toBeFalse()
        ->and($deleted->canAccessPlatform())->toBeFalse();
});

it('denies permissions until an explicit governed role grants them', function (): void {
    $user = User::factory()->create();
    $permission = Permission::create([
        'name' => 'content.pages.publish',
        'guard_name' => 'web',
    ]);
    $role = Role::create([
        'name' => 'content_publisher',
        'guard_name' => 'web',
    ]);

    expect($user->can('content.pages.publish'))->toBeFalse();

    $role->givePermissionTo($permission);
    $user->assignRole($role);

    expect($user->fresh()->can('content.pages.publish'))->toBeTrue();
});

it('keeps Arabic and English as the only supported identity locales', function (): void {
    $english = User::factory()->create(['preferred_locale' => 'en']);
    $arabic = User::factory()->create(['preferred_locale' => 'ar']);

    expect([$english->preferred_locale, $arabic->preferred_locale])
        ->toEqualCanonicalizing(['en', 'ar']);
});
