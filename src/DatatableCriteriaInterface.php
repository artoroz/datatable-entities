<?php

namespace Artoroz\Datatable;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;

interface DatatableCriteriaInterface
{
    /**
     * @param QueryBuilder $builder
     *
     * @return DatatableCriteriaInterface
     */
    public function filter(QueryBuilder $builder): DatatableCriteriaInterface;

    /**
     * @param QueryBuilder $builder
     *
     * @return DatatableCriteriaInterface
     */
    public function search(QueryBuilder $builder): DatatableCriteriaInterface;

    /**
     * @param QueryBuilder $builder
     *
     * @return DatatableCriteriaInterface
     */
    public function order(QueryBuilder $builder): DatatableCriteriaInterface;

    /**
     * @param Collection $options
     *
     * @return QueryBuilder
     */
    public function createBuilder(Collection $options): QueryBuilder;
}
