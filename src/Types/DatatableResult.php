<?php
namespace Artoroz\Datatable\Types;

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
     * @var Request
     */
    private $request;

    public function __construct(DatatableResponse $response, Request $request)
    {
        $this->response = $response;
        $this->request = $request;
    }

    protected function getMatches() : Collection
    {
        $criteria = $this->criteriaClass->createBuilder($this->options);
        $this->response->recordsTotal = (clone $criteria)->select('count(employee.id)')->getQuery()->getSingleScalarResult();

        $this->attachFilters($criteria);
        $this->response->recordsFiltered = (clone $criteria)->select('count(employee.id)')->getQuery()->getSingleScalarResult();

        return new ArrayCollection($criteria->getQuery()->getResult());
    }

    public function getResultSet()
    {
        $matches = $this->getMatches();
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