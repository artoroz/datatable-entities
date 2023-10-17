<?php

namespace Artoroz\Datatable;

/**
 * @phpstan-import-type DataTableQueryBuilder from DatatableRepositoryInterface
 */
interface DatatableCriteriaInterface
{
    /**
     * @param DataTableQueryBuilder $builder
     *
     * @return DatatableCriteriaInterface
     */
    public function filter($builder): DatatableCriteriaInterface;

    /**
     * @param DataTableQueryBuilder $builder
     *
     * @return DatatableCriteriaInterface
     */
    public function search($builder): DatatableCriteriaInterface;

    /**
     * @param DataTableQueryBuilder $builder
     *
     * @return DatatableCriteriaInterface
     */
    public function order($builder): DatatableCriteriaInterface;
}
