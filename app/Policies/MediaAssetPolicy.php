<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MediaAsset;
use App\Models\User;

final class MediaAssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasAnyPermission($user, [
            'media.view',
            'media.upload',
            'media.edit',
            'media.archive',
            'media.restore',
            'media.delete',
            'content.manage',
        ]);
    }

    public function view(User $user, MediaAsset $mediaAsset): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->hasAnyPermission($user, ['media.upload', 'content.manage']);
    }

    public function update(User $user, MediaAsset $mediaAsset): bool
    {
        return $this->hasAnyPermission($user, ['media.edit', 'content.manage']);
    }

    public function archive(User $user, MediaAsset $mediaAsset): bool
    {
        return $this->hasAnyPermission($user, ['media.archive', 'content.manage']);
    }

    public function restore(User $user, MediaAsset $mediaAsset): bool
    {
        return $this->hasAnyPermission($user, ['media.restore', 'content.manage']);
    }

    public function delete(User $user, MediaAsset $mediaAsset): bool
    {
        return $this->hasAnyPermission($user, ['media.delete', 'content.manage']);
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
