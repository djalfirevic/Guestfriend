<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    /**
     * @param string $apiToken
     * @return mixed
     */
    public function findByApiToken(string $apiToken)
    {
        return $this->builder->where('api_token', $apiToken)->first();
    }
}
