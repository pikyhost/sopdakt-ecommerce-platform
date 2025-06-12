<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Auth\User;

class NotificationService
{
    /**
     * Get user notifications with optional filtering by status.
     *
     * @param  User  $user
     * @param  string|null  $status ('unread', 'read', or null)
     * @return EloquentCollection
     */
    public function getNotifications(User $user, ?string $status = null): EloquentCollection
    {
        return $user->notifications()
            ->when($status === 'unread', fn($query) => $query->whereNull('read_at'))
            ->when($status === 'read', fn($query) => $query->whereNotNull('read_at'))
            ->latest()
            ->get();
    }

    /**
     * Validate notification IDs to ensure they belong to the user.
     *
     * @param  User  $user
     * @param  array  $notificationIds
     * @return EloquentCollection
     */
    public function validateNotificationIds(User $user, array $notificationIds): EloquentCollection
    {
        $validNotifications = $user->notifications()->whereIn('id', $notificationIds)->get();

        if ($validNotifications->count() !== count($notificationIds)) {
            abort(422, __('Some notification IDs are invalid or do not belong to the user.'));
        }

        return $validNotifications;
    }

    /**
     * Mark notifications as read.
     *
     * @param  User  $user
     * @param  array  $notificationIds
     * @return int
     */
    public function markAsRead(User $user, array $notificationIds): int
    {
        return $this->validateNotificationIds($user, $notificationIds)
            ->each(fn($notification) => $notification->markAsRead())
            ->count();
    }

    /**
     * Mark all notifications as read for the user.
     *
     * @param  User  $user
     * @return int
     */
    public function markAllAsRead(User $user): int
    {
        return $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);
    }

    /**
     * Delete notifications by IDs.
     *
     * @param  User  $user
     * @param  array  $notificationIds
     * @return int
     */
    public function deleteNotifications(User $user, array $notificationIds): int
    {
        return $this->validateNotificationIds($user, $notificationIds)->each->delete()->count();
    }

    /**
     * Delete all notifications for the user.
     *
     * @param  User  $user
     * @return int
     */
    public function deleteAllNotifications(User $user): int
    {
        return $user->notifications()->delete();
    }
}
