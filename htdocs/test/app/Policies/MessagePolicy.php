<?php

namespace App\Policies;

use App\User;
use App\Message;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view messages.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->isAdmin() || $user->active;
    }

    /**
     * Determine whether the user can view the message.
     *
     * @param  \App\User  $user
     * @param  \App\Message  $message
     * @return mixed
     */
    public function view(User $user, Message $message)
    {
        return $user->isAdmin() || $message->from_user_id == $user->id || $message->to_user_id == $user_id;
    }

    /**
     * Determine whether the user can create messages.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->active;
    }

    /**
     * Determine whether the user can update the message.
     *
     * @param  \App\User  $user
     * @param  \App\Message  $message
     * @return mixed
     */
    public function update(User $user, Message $message)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the message.
     *
     * @param  \App\User  $user
     * @param  \App\Message  $message
     * @return mixed
     */
    public function delete(User $user, Message $message)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can answer the message.
     *
     * @param  \App\User  $user
     * @param  \App\Message  $message
     * @return mixed
     */
    public function answer(User $user, Message $message)
    {
        // Admin can answer to anything
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->active) {
            return false;
        }

        $history = $message->getHistory();

        $lastMessage = $history->last();

        // Cannot answer to own message
        if ($lastMessage->from_user_id == $user->id) {
            return false;
        }

        // Check requests only have question and answer
        if ($lastMessage->type == 'check_request' && $history->count() == 2) {
            return false;
        }

        if (!$lastMessage->to_user_id) {
            // Teachers can answer to fresh check requests
            if ($lastMessage->type == 'check_request' && $user->isTeacher()) {
                return true;
            }

            // No one else can answer messages withour receiver (except admins)
            return false;
        }

        // Cannot answer foreign conversations
        if ($lastMessage->to_user_id != $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can mark message as read.
     *
     * @param  \App\User  $user
     * @param  \App\Message  $message
     * @return mixed
     */
    public function markRead(User $user, Message $message)
    {
        return $user->id == $message->to_user_id;
    }

    /**
     * Determine whether the user set rating to message
     *
     * @param  \App\User  $user
     * @param  \App\Message  $message
     * @return mixed
     */
    public function setRating(User $user, Message $message)
    {
        return $user->id == $message->from_user_id && $user->active;
    }

    /**
     * Determine whether the teacher can mark message as taken.
     *
     * @param  \App\User  $user
     * @param  \App\Message  $message
     * @return mixed
     */
    public function markTaken(User $user, Message $message)
    {
        return $user->isTeacher() && $user->active;
    }
}
