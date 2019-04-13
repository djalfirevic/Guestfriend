<?php

namespace App\Observers;

use App\Mails\UserMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Class UserObserver
 *
 * @package App\Observers
 */
class UserObserver
{
    /**
     * @param User $user
     */
    public function created(User $user): void
    {
        Mail::send(
            new UserMail(
                $user->email,
                'User account created',
                'Your account has been created.'
            )
        );
    }

    /**
     * @param User $user
     */
    public function updated(User $user): void
    {
        Mail::send(
            new UserMail(
                $user->email,
                'User account updated',
                'Your account has been updated.'
            )
        );
    }

    /**
     * @param User $user
     */
    public function deleted(User $user): void
    {
        Mail::send(
            new UserMail(
                $user->email,
                'User account deleted',
                'Your account has been deleted.'
            )
        );
    }
}
