<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CustomFilamentComment;

class CustomFilamentCommentPolicy
{
    /**
     * Determine if the user can view any comments.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view a specific comment.
     */
    public function view(User $user, CustomFilamentComment $comment): bool
    {
        return true;
    }

    /**
     * Determine if the user can create a new comment.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin', 'client']);
    }

    /**
     * Determine if the user can update the comment.
     */
    public function update(User $user, CustomFilamentComment $comment): bool
    {
        return $user->hasRole(['super_admin', 'admin']) || $user->id === $comment->user_id;
    }

    /**
     * Determine if the user can delete the comment.
     */
    public function delete(User $user, CustomFilamentComment $comment): bool
    {
        return $user->hasRole(['super_admin', 'admin']) || $user->id === $comment->user_id;
    }

    /**
     * Determine if the user can approve comments.
     */
    public function approve(User $user, CustomFilamentComment $comment): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }

    /**
     * Determine if the user can reject comments.
     */
    public function reject(User $user, CustomFilamentComment $comment): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }
}
