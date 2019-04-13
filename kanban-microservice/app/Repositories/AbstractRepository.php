<?php

namespace App\Repositories;

use App\Contracts\RepositoryInterface;
use App\Exceptions\AppException;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

/**
 * Class AbstractRepository
 *
 * @package App\Repositories
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * AbstractRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->builder = $model->newQuery();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        return $this->builder->findOrFail($id);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findWithTrashed(int $id)
    {
        return $this->model->withTrashed()->findOrFail($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data = [])
    {
        return $this->builder->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data = [])
    {
        $record = $this->builder->findOrFail($id);
        $record->update($data);

        return $record;
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws Exception
     */
    public function delete(int $id)
    {
        $record = $this->builder->findOrFail($id);
        return $record->delete();
    }

    /**
     * @param array $data
     * @return array
     * @throws AppException
     */
    public function search(array $data = [])
    {
        $builder = $this->builder;
        $searchable = $this->model->getSearchableAttributes();

        if (isset($data['query'])) {
            $q = $data['query'];
            $builder->where(function ($builder) use ($searchable, $q) {
                foreach ($searchable as $attribute) {
                    $builder->orWhere($attribute, 'LIKE', '%' . $q . '%');
                }
            });
        }

        if (isset($data['filter'])) {
            foreach ($data['filter'] as $key => $value) {
                $this->validateInput($searchable, $key, 'Invalid filter param "' . $key . '".');
                $builder->where($key, $value);
            }
        }

        $sorting = 'ASC';
        if (isset($data['sorting'])) {
            $this->validateInput(['asc', 'desc'], $data['sorting'], 'Invalid sorting param.');
            $sorting = $data['sorting'];
        }

        if (isset($data['order_by'])) {
            $this->validateInput($searchable, $data['order_by'], 'Invalid ordering param.');
            $builder->orderBy($data['order_by'], $sorting);
        }

        $page = 1;
        if (isset($data['page'])) {
            $page = $data['page'];
        }

        $limit = 20;
        if (isset($data['limit'])) {
            $limit = $data['limit'];
        }

        $builder->offset(($page - 1) * $limit)->limit($limit);

        return $builder->get()->toArray();
    }

    /**
     * @param array $range
     * @param string $param
     * @param string $message
     * @throws AppException
     */
    protected function validateInput(array $range, string $param, string $message)
    {
        if (!in_array($param, $range)) {
            throw new AppException($message, JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
