<?php

namespace App\Policies;

use App\User;
use App\Models\SysUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class SysUserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the sysUser.
     *
     * @param User $user
     * @param SysUser $sysUser
     * @return mixed
     */
    public function view(User $user, SysUser $sysUser)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can create sysUsers.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //

    }

    /**
     * Determine whether the user can update the sysUser.
     *
     * @param User $user
     * @param SysUser $sysUser
     * @return mixed
     */
    public function update(User $user, SysUser $sysUser)
    {
        //
    }

    /**
     * Determine whether the user can delete the sysUser.
     *
     * @param User $user
     * @param SysUser $sysUser
     * @return mixed
     */
    public function delete(User $user, SysUser $sysUser)
    {
        //
    }
}
