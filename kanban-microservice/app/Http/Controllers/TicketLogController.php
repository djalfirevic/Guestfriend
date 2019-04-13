<?php

namespace App\Http\Controllers;

use App\Contracts\FindTicketHistoryRepositoryInterface;
use App\Transformers\TicketLogTransformer;
use Illuminate\Http\JsonResponse;

/**
 * Class TicketLogController
 *
 * @package App\Http\Controllers
 */
class TicketLogController extends Controller
{
    /**
     * @var FindTicketHistoryRepositoryInterface
     */
    protected $ticketLogRepository;

    /**
     * @var TicketLogTransformer
     */
    protected $ticketLogTransformer;

    /**
     * TicketLogController constructor.
     *
     * @param FindTicketHistoryRepositoryInterface $ticketLogRepository
     * @param TicketLogTransformer $ticketLogTransformer
     */
    public function __construct(
        FindTicketHistoryRepositoryInterface $ticketLogRepository,
        TicketLogTransformer $ticketLogTransformer
    ) {
        $this->ticketLogRepository = $ticketLogRepository;
        $this->ticketLogTransformer = $ticketLogTransformer;
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function history(int $id): JsonResponse
    {
        $result = $this->ticketLogRepository->findHistory($id);

        return $this->responseCollection($result, $this->ticketLogTransformer);
    }
}
