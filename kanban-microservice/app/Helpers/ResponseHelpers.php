<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

/**
 * Trait ResponseHelpers
 *
 * @package App\Helpers
 */
trait ResponseHelpers
{
    /**
     * @param $collection
     * @param TransformerAbstract $transformer
     * @param int $statusCode
     * @param null $type
     * @return JsonResponse
     */
    protected function responseCollection(
        $collection,
        TransformerAbstract $transformer,
        $statusCode = JsonResponse::HTTP_OK,
        $type = null
    ) {
        $resource = new Collection($collection, $transformer, $type ?? 'results');
        $data = app(Manager::class)->createData($resource)->toArray();

        return $this->responseJson($data, $statusCode);
    }

    /**
     * @param null $data
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function responseJson($data = null, $statusCode = JsonResponse::HTTP_OK)
    {
        return response()->json($data, $statusCode);
    }
}
