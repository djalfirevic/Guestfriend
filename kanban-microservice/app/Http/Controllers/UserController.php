<?php

namespace App\Http\Controllers;

use App\Contracts\UserRepositoryInterface;
use App\Exceptions\AppException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * AbstractController constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function get(int $id): JsonResponse
    {
        $result = $this->userRepository->find($id);

        return $this->responseJson($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $this->validateCreateRequest($request);
        $result = $this->userRepository->create($request->all('name', 'email'));

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
        $this->validateUpdateRequest($request, $id);
        $result = $this->userRepository->update($id, $request->all('name', 'email'));

        return $this->responseJson($result);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $this->userRepository->delete($id);

        return $this->responseJson([], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AppException
     */
    public function search(Request $request): JsonResponse
    {
        $results = $this->userRepository->search(
            $request->all(['query', 'filter', 'sorting', 'order_by', 'page', 'limit'])
        );

        return $this->responseJson($results);
    }

    /**
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    private function validateCreateRequest(Request $request): array
    {
        return $this->validate($request, [
            'name'  => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return array
     * @throws ValidationException
     */
    private function validateUpdateRequest(Request $request, int $id): array
    {
        return $this->validate($request, [
            'name'  => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email,' . $id],
        ]);
    }
}
