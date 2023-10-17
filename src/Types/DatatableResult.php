<?php

namespace Artoroz\Datatable\Types;

use Artoroz\Datatable\Table;
use Artoroz\Datatable\DatatableRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Artoroz\Datatable\DatatableCriteriaInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Artoroz\Datatable\Response\DatatableResponse;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @phpstan-import-type DataTableQueryBuilder from DatatableRepositoryInterface
 */
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
    protected $request;

    public function __construct(Table $table, Request $request)
    {
        $this->response = new DatatableResponse();
        $this->request = $request;
    }

    protected function getMatches(): Collection
    {
        $criteria = $this->repository->createBuilder($this->options);
        if (method_exists($this->repository, 'setupPermissions')) {
            $this->repository->setupPermissions($criteria, $this->user, $this->options);
        }

        $this->response->recordsTotal = $this->repository->countResults(clone $criteria);

        $this->attachFilters($criteria);
        $this->response->recordsFiltered = $this->repository->countResults(clone $criteria);

        if ($criteria instanceof QueryBuilder) {
            $matches = $criteria->getQuery()
                ->getResult();
        } else {
            $matches = [];

            foreach ($criteria->execute()->fetchAllAssociative() as $record) {
                $matches[] = (object) $record;
            }
        }
        return new ArrayCollection($matches);
    }

    public function getResultSet()
    {
        $matches = $this->getMatches();

        $orderProperty = $this->criteriaClass->getDataOrderProperty();
        $orderDirection = $this->criteriaClass->getDataOrderDirection();
        $class = $this->criteriaClass->getTable()->getEntityClassName();

        // When sorting on a non-existing database field (dynamic column)
        if ($orderProperty && $orderDirection) {
            $iterator = $matches->getIterator();
            $iterator->uasort(function ($a, $b) use ($orderProperty, $orderDirection) {

                $propertyAccessor = PropertyAccess::createPropertyAccessor();
                $aValue = $propertyAccessor->getValue($a, $orderProperty);
                $bValue = $propertyAccessor->getValue($b, $orderProperty);

                if ($orderDirection == 'DESC') {
                    return strnatcmp($bValue, $aValue);
                } else {
                    return strnatcmp($aValue, $bValue);
                }
            });
            $matches = new ArrayCollection(array_values(iterator_to_array($iterator)));
        }

        $this->response->setData($matches);
        $this->response->setFields($this->fields);

        return $this->response->getResponse();
    }

    /**
     * @param DataTableQueryBuilder $builder
     */
    public function attachFilters($builder): void
    {
        $this->criteriaClass
            ->filter($builder)
            ->search($builder)
            ->order($builder)
            ->pagination($builder)
        ;
    }
}
