<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Permissions\ReviewPermissions;

class ReviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['superadmin', 'admin']) &&
               $user->can(ReviewPermissions::VIEW_ANY);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Review $review): bool
    {
        if (!$user->can(ReviewPermissions::VIEW)) {
            return false;
        }

        // Superadmin can view all reviews
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // Admin can only view reviews for their restaurants
        if ($user->hasRole('admin')) {
            return $user->restaurants->contains($review->restaurant_id);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        if (!$user->can(ReviewPermissions::DELETE)) {
            return false;
        }

        // Superadmin can delete any review
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // Admin can only delete reviews for their restaurants
        if ($user->hasRole('admin')) {
            return $user->restaurants->contains($review->restaurant_id);
        }

        return false;
    }
}
