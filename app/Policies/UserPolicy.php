<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasAllRoles('المدير')||$user->can('view_any_user'))
        {
            return $user->can('view_any_user');

        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function view(User $user): bool
    {
        if ($user->hasAllRoles('المدير')||$user->can('view_user'))
        {
            return $user->can('view_user');

        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        if ($user->hasAllRoles('المدير'))
        {
            return $user->can('create_user');

        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function update(User $user): bool
    {
        if ($user->hasAllRoles('المدير'))
        {
            return $user->can('update_user');

        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        if ($user->hasAllRoles('المدير'))
        {
            return $user->can('delete_user');

        }
        return false;
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        if ($user->hasAllRoles('المدير')||$user->can('{{ DeleteAny }}'))
        {
            return $user->can('{{ DeleteAny }}');

        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        if ($user->hasAllRoles('المدير')||$user->can('{{ ForceDelete }}'))
        {
            return $user->can('{{ ForceDelete }}');

        }
        return false;
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        if ($user->hasAllRoles('المدير')||$user->can('{{ ForceDeleteAny }}'))
        {
            return $user->can('{{ ForceDeleteAny }}');

        }
        return false;
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        if ($user->hasAllRoles('المدير')||$user->can('{{ Restore }}'))
        {
            return $user->can('{{ Restore }}');

        }
        return false;
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        if ($user->hasAllRoles('المدير')||$user->can('{{ RestoreAny }}'))
        {
            return $user->can('{{ RestoreAny }}');

        }
        return false;
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function replicate(User $user): bool
    {
        if ($user->hasAllRoles('المدير')||$user->can('{{ Replicate }}'))
        {
            return $user->can('{{ Replicate }}');

        }
        return false;
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        if ($user->hasAllRoles('المدير')||$user->can('{{ Reorder }}'))
        {
            return $user->can('{{ Reorder }}');

        }
        return false;
    }
}
