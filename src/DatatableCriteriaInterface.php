<?php

namespace Artoroz\Datatable;

use Doctrine\DBAL\Query\QueryBuilder as DbalQueryBuilder;
use Doctrine\ORM\QueryBuilder;
use Somnambulist\Components\CTEBuilder\ExpressionBuilder;

interface DatatableCriteriaInterface
{
    public function filter(ExpressionBuilder|QueryBuilder|DbalQueryBuilder $builder): DatatableCriteriaInterface;
    public function search(ExpressionBuilder|QueryBuilder|DbalQueryBuilder $builder): DatatableCriteriaInterface;
    public function order(ExpressionBuilder|QueryBuilder|DbalQueryBuilder$builder): DatatableCriteriaInterface;
    public function pagination(ExpressionBuilder|QueryBuilder|DbalQueryBuilder$builder): DatatableCriteriaInterface;
    public function getTable(): Table;
    public function getDataOrderProperty(): ?string;
    public function getDataOrderDirection(): ?string;
}
