<?php

namespace App\Repositories;

use App\Contracts\TicketLogRepositoryInterface;

/**
 * Class TicketLogRepository
 *
 * @package App\Repositories
 */
class TicketLogRepository extends AbstractRepository implements TicketLogRepositoryInterface
{
    /**
     * @param string $action
     * @param int $ticketId
     * @param string $requestedAt
     * @param array $details
     * @return mixed
     */
    public function store(string $action, int $ticketId, string $requestedAt, array $details = [])
    {
        return $this->builder->create([
            'action'       => $action,
            'ticket_id'    => $ticketId,
            'details'      => json_encode($details, JSON_FORCE_OBJECT),
            'requested_at' => $requestedAt,
        ]);
    }

    /**
     * @param int $ticketId
     * @return mixed
     */
    public function findHistory(int $ticketId)
    {
        return $this->builder->where('ticket_id', $ticketId)->get();
    }
}
