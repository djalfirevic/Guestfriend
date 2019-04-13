<?php

namespace App\Events;

use App\Models\Ticket;

/**
 * Class AbstractTicketEvent
 *
 * @package App\Events
 */
abstract class AbstractTicketEvent extends Event
{
    /**
     * @var Ticket
     */
    public $ticket;

    /**
     * AbstractTicketEvent constructor.
     *
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }
}
