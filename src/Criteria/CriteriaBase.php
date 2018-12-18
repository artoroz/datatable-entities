<?php
namespace Artoroz\Datatable\Criteria;

use Doctrine\Common\Collections\ArrayCollection;
use Artoroz\Datatable\DatatableCriteriaInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\Collection;
use Artoroz\Datatable\Types\Field\Field;
use Artoroz\Datatable\Types\Table;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use ErrorException;

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
     * @var EntityManager $em
     */
    protected $em;

    /**
     * CriteriaBase constructor.
     *
     * @param ArrayCollection $fields
     * @param Request $request
     * @param EntityManager $em
     */
    public function __construct(ArrayCollection $fields, Request $request, EntityManager $em)
    {
        $this->request = $request;
        $this->fields  = $fields;
        $this->em      = $em;
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
                $field->queryField => $direction
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

    /**
     * @param Collection $options
     *
     * @return QueryBuilder
     */
    public function createBuilder(Collection $options): QueryBuilder
    {
        return $this->em->createQueryBuilder();
    }
}
