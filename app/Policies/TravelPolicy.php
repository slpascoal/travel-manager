<?php

namespace App\Policies;

use App\Models\Travel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TravelPolicy
{
    /**
     * Determine whether the user can permanently delete the model.
     */
    public function modify(User $user, Travel $travel): Response
    {
        return $user->id === $travel->user_id
            ? Response::allow()
            : Response::deny('You do not own this post');
    }
}
