<?php

namespace Artoroz\Datatable;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;

interface DatatableRepositoryInterface
{
    /**
     * @param Collection $options
     *
     * @return QueryBuilder
     */
    public function createBuilder(Collection $options): QueryBuilder;

    /**
     * @param QueryBuilder $builder
     *
     * @return int
     */
    public function countResults(QueryBuilder $builder): int;
}
