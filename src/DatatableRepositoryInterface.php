<?php

namespace Artoroz\Datatable;

use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Query\QueryBuilder as DbalQueryBuilder;
use Doctrine\ORM\QueryBuilder;
use Somnambulist\Components\CTEBuilder\ExpressionBuilder;

interface DatatableRepositoryInterface
{
    /**
     * @param Collection<string, mixed> $options
     */
    public function createBuilder(Collection $options): ExpressionBuilder|QueryBuilder|DbalQueryBuilder;

    public function countResults(ExpressionBuilder|QueryBuilder|DbalQueryBuilder $builder): int;
}
