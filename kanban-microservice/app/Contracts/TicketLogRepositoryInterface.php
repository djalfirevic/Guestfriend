<?php

namespace App\Contracts;

/**
 * Interface TicketLogRepositoryInterface
 *
 * @package App\Contracts
 */
interface TicketLogRepositoryInterface extends RepositoryInterface, StoreTicketLogRepositoryInterface, FindTicketHistoryRepositoryInterface
{

}
