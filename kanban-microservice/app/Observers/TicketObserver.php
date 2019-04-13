<?php

namespace App\Observers;

use App\Contracts\StoreTicketLogRepositoryInterface;
use App\Mails\UserMail;
use App\Models\Ticket;
use App\Repositories\TicketLogRepository;
use Illuminate\Support\Facades\Mail;

/**
 * Class TicketObserver
 *
 * @package App\Observers
 */
class TicketObserver
{
    /**
     * @var TicketLogRepository
     */
    protected $ticketLogRepository;

    /**
     * TicketObserver constructor.
     *
     * @param StoreTicketLogRepositoryInterface $ticketLogRepository
     */
    public function __construct(StoreTicketLogRepositoryInterface $ticketLogRepository)
    {
        $this->ticketLogRepository = $ticketLogRepository;
    }

    /**
     * @param Ticket $ticket
     */
    public function created(Ticket $ticket): void
    {
        $this->ticketLogRepository->store(
            Ticket::CREATE_ACTION,
            $ticket->id,
            $ticket->updated_at,
            $ticket->prepareForLog()
        );

        Mail::send(
            new UserMail(
                $ticket->user->email,
                'Ticket assigned',
                'Your have been assigned a ticket.'
            )
        );
    }

    /**
     * @param Ticket $ticket
     */
    public function updated(Ticket $ticket): void
    {
        $this->ticketLogRepository->store(
            Ticket::UPDATE_ACTION,
            $ticket->id,
            $ticket->updated_at,
            $ticket->prepareForLog()
        );

        if (key_exists('user_id', $ticket->getDirty())) {
            Mail::send(
                new UserMail(
                    $ticket->user->email,
                    'Ticket assigned',
                    'Your have been assigned a ticket.'
                )
            );
        }
    }

    /**
     * @param Ticket $ticket
     */
    public function deleted(Ticket $ticket): void
    {
        $this->ticketLogRepository->store(
            Ticket::DELETE_ACTION,
            $ticket->id,
            $ticket->updated_at
        );

        Mail::send(
            new UserMail(
                $ticket->user->email,
                'Ticket deleted',
                'Your ticket has been deleted.'
            )
        );
    }
}
