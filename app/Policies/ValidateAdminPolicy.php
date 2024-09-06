<?php

namespace App\Policies;

use App\Models\User;

class ValidateAdminPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function ValidateAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }
}
