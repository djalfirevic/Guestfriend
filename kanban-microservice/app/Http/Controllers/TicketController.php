<?php

namespace App\Http\Controllers;

use App\Contracts\TicketRepositoryInterface;
use App\Exceptions\AppException;
use App\Repositories\TicketRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class TicketController
 *
 * @package App\Http\Controllers
 */
class TicketController extends Controller
{
    /**
     * @var TicketRepository
     */
    protected $ticketRepository;

    /**
     * TicketController constructor.
     *
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(TicketRepositoryInterface $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function get(int $id): JsonResponse
    {
        $result = $this->ticketRepository->find($id);

        return $this->responseJson($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $this->validateRequest($request);
        $result = $this->ticketRepository->create(
            $request->all('title', 'description', 'priority', 'user_id', 'lane_id')
        );

        return $this->responseJson($result, JsonResponse::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $this->validateRequest($request);
        $result = $this->ticketRepository->update(
            $id,
            $request->all('title', 'description', 'priority', 'user_id', 'lane_id')
        );

        return $this->responseJson($result);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(int $id): JsonResponse
    {
        $this->ticketRepository->delete($id);

        return $this->responseJson([], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AppException
     */
    public function search(Request $request): JsonResponse
    {
        $results = $this->ticketRepository->search(
            $request->all(['query', 'filter', 'sorting', 'order_by', 'page', 'limit'])
        );

        return $this->responseJson($results);
    }

    /**
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    private function validateRequest(Request $request): array
    {
        return $this->validate($request, [
            'title'       => ['required', 'string', 'max:191'],
            'description' => ['required', 'string'],
            'priority'    => ['required', 'numeric'],
            'user_id'     => ['required', 'numeric', 'exists:users,id'],
            'lane_id'     => ['required', 'numeric', 'exists:lanes,id'],
        ]);
    }
}
