<?php

namespace Artoroz\Datatable;

use Doctrine\Common\Collections\Collection;

/**
 * @phpstan-type DataTableQueryBuilder \Somnambulist\CTEBuilder\ExpressionBuilder|\Doctrine\ORM\QueryBuilder|\Doctrine\DBAL\Query\QueryBuilder
 */
interface DatatableRepositoryInterface
{
    /**
     * @param Collection $options
     *
     * @return DataTableQueryBuilder
     */
    public function createBuilder(Collection $options);

    /**
     * @param DataTableQueryBuilder $builder
     *
     * @return int
     */
    public function countResults($builder): int;
}
