<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PaymentPoster;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPosterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_payment::poster');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PaymentPoster $paymentPoster): bool
    {
        return $user->can('view_payment::poster');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_payment::poster');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PaymentPoster $paymentPoster): bool
    {
        return $user->can('update_payment::poster');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaymentPoster $paymentPoster): bool
    {
        return $user->can('delete_payment::poster');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_payment::poster');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, PaymentPoster $paymentPoster): bool
    {
        return $user->can('force_delete_payment::poster');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_payment::poster');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, PaymentPoster $paymentPoster): bool
    {
        return $user->can('restore_payment::poster');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_payment::poster');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, PaymentPoster $paymentPoster): bool
    {
        return $user->can('replicate_payment::poster');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_payment::poster');
    }
}
