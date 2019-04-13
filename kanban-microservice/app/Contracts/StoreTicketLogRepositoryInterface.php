<?php

namespace App\Contracts;

/**
 * Interface StoreTicketLogRepositoryInterface
 *
 * @package App\Contracts
 */
interface StoreTicketLogRepositoryInterface
{
    /**
     * @param string $action
     * @param int $ticketId
     * @param string $requestedAt
     * @param array $details
     * @return mixed
     */
    public function store(string $action, int $ticketId, string $requestedAt, array $details = []);
}
