<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\NavigationMenu;
use App\Models\User;

final class NavigationPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasAnyPermission($user, [
            'navigation.view',
            'navigation.edit',
            'navigation.publish',
            'navigation.delete',
            'content.manage',
        ]);
    }

    public function view(User $user, NavigationMenu $navigationMenu): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->hasAnyPermission($user, ['navigation.edit', 'content.manage']);
    }

    public function update(User $user, NavigationMenu $navigationMenu): bool
    {
        return $this->hasAnyPermission($user, ['navigation.edit', 'content.manage']);
    }

    public function publish(User $user, NavigationMenu $navigationMenu): bool
    {
        return $this->hasAnyPermission($user, ['navigation.publish', 'content.manage']);
    }

    public function delete(User $user, NavigationMenu $navigationMenu): bool
    {
        return $this->hasAnyPermission($user, ['navigation.delete', 'content.manage']);
    }

    /** @param list<string> $permissions */
    private function hasAnyPermission(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }
}
