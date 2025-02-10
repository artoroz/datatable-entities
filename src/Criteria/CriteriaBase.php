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

            foreach ($columns as $id => $column) {
                if (empty($column['search']['value'])) {
                    continue;
                }
                $field = $column['name'];

                $fields[$field] = $column['search']['value'];
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
        $order = (array) $this->request->get('order');
        try {
            if (! array_key_exists(0, $order) ||
                ! array_key_exists('column', $order[0])) {
                return false;
            }
            $field = $this->getFieldByNumber($order[0]['column']);
            if (is_null($field)) {
                return false;
            }
            $direction = $order[0]['dir'] == 'asc' ? 'ASC': 'DESC';

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
        $start = $this->request->get('start') ?? 0;
        $length = $this->request->get('length') ?? 10;
        $builder
            ->setFirstResult($start)
            ->setMaxResults($length)
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
