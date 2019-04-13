<?php

namespace App\Transformers;

use App\Contracts\LaneRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Models\TicketLog;
use League\Fractal\TransformerAbstract;

/**
 * Class TicketLogTransformer
 *
 * @package App\Transformers
 */
class TicketLogTransformer extends TransformerAbstract
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var LaneRepositoryInterface
     */
    private $laneRepository;

    /**
     * TicketLogTransformer constructor.
     *
     * @param UserRepositoryInterface $userRepository
     * @param LaneRepositoryInterface $laneRepository
     */
    public function __construct(UserRepositoryInterface $userRepository, LaneRepositoryInterface $laneRepository)
    {
        $this->userRepository = $userRepository;
        $this->laneRepository = $laneRepository;
    }

    /**
     * @param TicketLog $ticketLog
     * @return array
     */
    public function transform(TicketLog $ticketLog)
    {
        $details = json_decode($ticketLog->details);
        $changes = [];

        if (isset($details->title)) {
            $changes['title'] = $details->title;
        }

        if (isset($details->description)) {
            $changes['description'] = $details->description;
        }

        if (isset($details->priority)) {
            $changes['priority'] = $details->priority;
        }

        if (isset($details->user_id)) {
            $changes['user_assigned'] = $this->userRepository->findWithTrashed($details->user_id)->name;
        }

        if (isset($details->lane_id)) {
            $changes['status'] = $this->laneRepository->findWithTrashed($details->lane_id)->name;
        }

        $result = [
            'action'       => $ticketLog->action,
            'requested_at' => $ticketLog->requested_at,
        ];

        if ($changes) {
            $result['details'] = $changes;
        }

        return $result;
    }
}
