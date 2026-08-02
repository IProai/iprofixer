<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ContentPage;
use App\Models\User;

final class ContentPagePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasAnyPermission($user, [
            'content.create',
            'content.edit',
            'content.review',
            'content.approve',
            'content.publish',
            'content.preview',
            'content.restore',
            'content.archive',
            'content.manage',
        ]);
    }

    public function view(User $user, ContentPage $contentPage): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->hasAnyPermission($user, ['content.create', 'content.manage']);
    }

    public function update(User $user, ContentPage $contentPage): bool
    {
        return $this->hasAnyPermission($user, ['content.edit', 'content.manage']);
    }

    public function review(User $user, ContentPage $contentPage): bool
    {
        return $this->hasAnyPermission($user, ['content.review', 'content.manage']);
    }

    public function approve(User $user, ContentPage $contentPage): bool
    {
        return $this->hasAnyPermission($user, ['content.approve', 'content.manage']);
    }

    public function publish(User $user, ContentPage $contentPage): bool
    {
        return $this->hasAnyPermission($user, ['content.publish', 'content.manage']);
    }

    public function preview(User $user, ContentPage $contentPage): bool
    {
        return $this->hasAnyPermission($user, [
            'content.preview',
            'content.edit',
            'content.review',
            'content.approve',
            'content.publish',
            'content.manage',
        ]);
    }

    public function restore(User $user, ContentPage $contentPage): bool
    {
        return $this->hasAnyPermission($user, ['content.restore', 'content.manage']);
    }

    public function delete(User $user, ContentPage $contentPage): bool
    {
        return $this->hasAnyPermission($user, ['content.archive', 'content.manage']);
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
