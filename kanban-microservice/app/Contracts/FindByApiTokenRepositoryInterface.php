<?php

namespace App\Contracts;

/**
 * Interface FindByApiTokenRepositoryInterface
 *
 * @package App\Contracts
 */
interface FindByApiTokenRepositoryInterface
{
    /**
     * @param string $apiToken
     * @return mixed
     */
    public function findByApiToken(string $apiToken);
}
