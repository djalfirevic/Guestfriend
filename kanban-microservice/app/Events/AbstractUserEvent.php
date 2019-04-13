<?php

namespace App\Events;

use App\Models\User;

/**
 * Class AbstractUserEvent
 *
 * @package App\Events
 */
abstract class AbstractUserEvent extends Event
{
    /**
     * @var User
     */
    public $user;

    /**
     * AbstractUserEvent constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
