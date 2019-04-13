<?php

namespace App\Contracts;

use App\Exceptions\AppException;

/**
 * Interface RepositoryInterface
 *
 * @package App\Contracts
 */
interface RepositoryInterface
{
    /**
     * @param int $id
     * @return mixed
     */
    public function find(int $id);

    /**
     * @param int $id
     * @return mixed
     */
    public function findWithTrashed(int $id);

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data = []);

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data = []);

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);

    /**
     * @param array $data
     * @return array
     * @throws AppException
     */
    public function search(array $data = []);
}
