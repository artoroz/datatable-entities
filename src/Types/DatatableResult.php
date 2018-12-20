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