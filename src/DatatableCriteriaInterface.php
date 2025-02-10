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

    /**
     * @param DataTableQueryBuilder $builder
     *
     * @return DatatableCriteriaInterface
     */
    public function pagination($builder): DatatableCriteriaInterface;

    public function getTable(): Table;
    public function getDataOrderProperty(): ?string;
    public function getDataOrderDirection(): ?string;
}
