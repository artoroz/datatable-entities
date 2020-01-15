<?php
namespace Artoroz\Datatable\Types;

use Artoroz\Datatable\Table;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Artoroz\Datatable\DatatableCriteriaInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Artoroz\Datatable\Response\DatatableResponse;

abstract class DatatableResult
{

    /**
     * @var DatatableResponse
     */
    protected $response;

    /**
     * @var DatatableCriteriaInterface $criteriaClass
     */
    protected $criteriaClass;

    /**
     * @var Request $request
     */
    private $request;

    public function __construct(Table $table, Request $request)
    {
        $this->response = new DatatableResponse();
        $this->request = $request;
    }

    protected function getMatches() : Collection
    {
        $criteria = $this->repository->createBuilder($this->options);
        if (method_exists($this->repository, 'setupPermissions')) {
            $this->repository->setupPermissions($criteria, $this->user, $this->options);
        }

        $this->response->recordsTotal = $this->repository->countResults(clone $criteria);

        $this->attachFilters($criteria);
        $this->response->recordsFiltered = $this->repository->countResults(clone $criteria);

        return new ArrayCollection($criteria->getQuery()->getResult());
    }

    public function getResultSet()
    {
        $matches = $this->getMatches();

        $orderProperty = $this->criteriaClass->getDataOrderProperty();
        $orderDirection = $this->criteriaClass->getDataOrderDirection();
        $class = $this->criteriaClass->getTable()->getEntityClassName();

        // When sorting on a non-existing database field (dynamic column)
        if ($orderProperty && $orderDirection) {
            $getter = "get" . ucfirst($orderProperty);

            if (method_exists($class, $getter)) {
                $iterator = $matches->getIterator();
                $iterator->uasort(function ($a, $b) use($getter, $orderDirection) {
                    if ($a->$getter() == $b->$getter()) {
                        return 0;
                    }
                    if ($orderDirection == "DESC") {
                        return (strcmp($a->$getter(), $b->$getter()) < 0 ? 1 : -1);
                    } else {
                        return (strcmp($a->$getter(), $b->$getter()) < 0 ? -1 : 1);
                    }
                });
                $matches = new ArrayCollection(array_values(iterator_to_array($iterator)));
            }
        }

        $this->response->setData($matches);
        $this->response->setFields($this->fields);

        return $this->response->getResponse();
    }

    public function attachFilters(QueryBuilder $builder): void
    {
        $this->criteriaClass
            ->filter($builder)
            ->search($builder)
            ->order($builder)
            ->pagination($builder)
        ;
    }
}