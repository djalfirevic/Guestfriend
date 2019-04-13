<?php

namespace App\Contracts;

/**
 * Interface FindTicketHistoryRepositoryInterface
 *
 * @package App\Contracts
 */
interface FindTicketHistoryRepositoryInterface
{
    /**
     * @param int $ticketId
     * @return mixed
     */
    public function findHistory(int $ticketId);
}
