<?php
namespace Artoroz\Datatable\Criteria;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use ErrorException;
use Symfony\Component\HttpFoundation\Request;
use Artoroz\Datatable\DatatableCriteriaInterface;
use Artoroz\Datatable\Types\Field\Field;
use Artoroz\Datatable\Types\Table;

abstract class CriteriaBase
{
    /**
     * @var Request $request;
     */
    protected $request;

    /**
     * @var ArrayCollection<Field> $fields;
     */
    protected $fields;

    /**
     * CriteriaBase constructor.
     *
     * @param ArrayCollection $fields
     * @param Request $request
     */
    public function __construct(ArrayCollection $fields, Request $request)
    {
        $this->request = $request;
        $this->fields  = $fields;
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
            $direction = $order[0]['dir'] == 'asc' ? 'ASC': 'DESC';
            return [
                $field->name => $direction
            ];
        } catch (ErrorException $e) {
            return [];
        }
    }

    protected function getFieldByNumber($column)
    {
        return $this->fields->get($column);
    }

    /**
     * @param Criteria $criteria
     *
     * @return DatatableCriteriaInterface
     */
    public function pagination(Criteria $criteria): DatatableCriteriaInterface
    {
        $start = $this->request->get('start') ?? 0;
        $length = $this->request->get('length') ?? 10;
        $criteria
            ->setFirstResult($start)
            ->setMaxResults($length)
        ;

        return $this;
    }

    /**
     * @return Criteria
     */
    public function createCriteria(array $options = []): Criteria
    {
        return new Criteria();
    }
}
