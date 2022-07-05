<?php
namespace Artoroz\Datatable\Criteria;

use Artoroz\Datatable\DatatableCriteriaInterface;
use Artoroz\Datatable\Table;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;
use Artoroz\Datatable;
use ErrorException;

abstract class CriteriaBase
{
    /**
     * @var Request $request;
     */
    protected $request;

    /**
     * @var string $prefix;
     */
    protected $prefix = '';

    /**
     * @var Table $table
     */
    protected $table;

    /**
     * @var string $dataOrderProperty
     */
    protected $dataOrderProperty;

    /**
     * @var string $dataOrderDirection
     */
    protected $dataOrderDirection;

    /**
     * CriteriaBase constructor.
     *
     * @param Table $table
     * @param Request $request
     */
    public function __construct($table, Request $request)
    {
        $this->table  = $table;
        $this->request = $request;
    }

    public function getSearchField()
    {
        return $this->request->get('search');
    }

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

    protected function getOrderBy()
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

    protected function getFieldByNumber($column)
    {
        return $this->table->get($column);
    }

    protected function getOption($key)
    {
        return $this->table->options->get($key);
    }

    /**
     * @param QueryBuilder $builder
     *
     * @return DatatableCriteriaInterface
     */
    public function pagination(QueryBuilder $builder): DatatableCriteriaInterface
    {
        $start = $this->request->get('start') ?? 0;
        $length = $this->request->get('length') ?? 10;
        $builder
            ->setFirstResult($start)
            ->setMaxResults($length)
        ;

        return $this;
    }

    public function getTable(): Datatable\Table
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
