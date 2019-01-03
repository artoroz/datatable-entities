<?php
namespace Artoroz\Datatable\Criteria;

use Doctrine\Common\Collections\ArrayCollection;
use Artoroz\Datatable\DatatableCriteriaInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\Collection;
use Artoroz\Datatable\Types\Field\Field;
use Doctrine\ORM\EntityManager;
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
        $order = $this->request->get('order');
        try {
            $field = $this->getFieldByNumber($order[0]['column']);
            if (is_null($field)) {
                return false;
            }
            $direction = $order[0]['dir'] == 'asc' ? 'ASC': 'DESC';
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
}
