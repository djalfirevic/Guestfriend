<?php

namespace App\Contracts;

/**
 * Interface SearchableModelInterface
 *
 * @package App\Contracts
 */
interface SearchableModelInterface
{
    /**
     * @return array
     */
    public function getSearchableAttributes(): array;
}
