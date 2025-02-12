<?php

declare(strict_types=1);

namespace Artoroz\Datatable\Criteria;

use Artoroz\Datatable\DatatableCriteriaInterface;
use Artoroz\Datatable\Table;
use Artoroz\Datatable\Types\Field\ColumnField;
use Doctrine\DBAL\Query\QueryBuilder as DbalQueryBuilder;
use Doctrine\ORM\QueryBuilder;
use ErrorException;
use Exception;
use Somnambulist\Components\CTEBuilder\ExpressionBuilder;
use Symfony\Component\HttpFoundation\Request;

abstract class CriteriaBase implements DatatableCriteriaInterface
{
    protected string $prefix = '';
    protected ?string $dataOrderProperty = null;
    protected ?string $dataOrderDirection = null;

    public function __construct(
        protected Table $table,
        protected Request $request,
    ) {
    }

    public function getSearchField(): mixed
    {
        return $this->request->get('search');
    }

    /**
     * @return array<string, mixed>|array{error:Exception}
     */
    protected function getFilterRequest() : array
    {
        $fields = [];
        try {
            $columns = $this->request->get('columns');

            if (! is_array($columns)) {
                return [];
            }

            foreach ($columns as $id => $column) {
                if (! is_array($column)) {
                    continue;
                }

                $field  = $column['name'] ?? null;
                $search = $column['search'];
                $searchValue = is_array($search)
                    ? $search['value'] ?? null
                    : null;

                if (! is_string($field) || empty($searchValue)) {
                    continue;
                }

                $fields[$field] = $searchValue;
            }

            return $fields;
        } catch (ErrorException $e) {
            return ['error' => $e];
        }
    }

    /**
     * @return array{0:string, 1:string}|false
     */
    protected function getOrderBy(): array|false
    {
        $order = $this->request->get('order');

        if (! is_array($order)) {
            return false;
        }

        try {
            $firstValue = $order[0] ?? null;

            if (! is_array($firstValue)) {
                return false;
            }

            $firstColumn = $firstValue['column'] ?? null;

            if (! is_int($firstColumn) && ! is_string($firstColumn)) {
                return false;
            }

            $field = $this->getFieldByNumber($firstColumn);
            if (is_null($field)) {
                return false;
            }
            $direction = ($firstValue['dir'] ?? 'asc') === 'asc' ? 'ASC': 'DESC';

            // Check if the propert exists in the Class for ordering a dynamic column
            if ($this->table->getEntityClassName()) {
                if (
                    ! property_exists($this->table->getEntityClassName(), $field->name) &&
                    strpos($field->queryField, '.') == 0
                ) {
                    $this->dataOrderProperty = $field->name;
                    $this->dataOrderDirection = $direction;
                    return false;
                }
            }

            return [
                $field->queryField,
                $direction
            ];
        } catch (Exception $e) {
            return false;
        }
    }

    protected function getFieldByNumber(int|string $column): ?ColumnField
    {
        return $this->table->get($column);
    }

    protected function getOption(string $key): mixed
    {
        return $this->table->options->get($key);
    }

    public function pagination(ExpressionBuilder|QueryBuilder|DbalQueryBuilder $builder): DatatableCriteriaInterface
    {
        $start = $this->request->get('start');
        $length = $this->request->get('length');
        $builder
            ->setFirstResult(is_numeric($start) ? (int) $start : 0)
            ->setMaxResults(is_numeric($length) ? (int) $length : 10)
        ;

        return $this;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getDataOrderProperty(): ?string
    {
        return $this->dataOrderProperty;
    }

    public function getDataOrderDirection(): ?string
    {
        return $this->dataOrderDirection;
    }
}
