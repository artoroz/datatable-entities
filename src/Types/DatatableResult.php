<?php

declare(strict_types=1);

namespace Artoroz\Datatable\Types;

use ArrayIterator;
use Artoroz\Datatable\DatatableCriteriaInterface;
use Artoroz\Datatable\DatatableRepositoryInterface;
use Artoroz\Datatable\Response\DatatableResponse;
use Artoroz\Datatable\Types\Field\ColumnField;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Query\QueryBuilder as DbalQueryBuilder;
use Doctrine\ORM\QueryBuilder;
use Somnambulist\Components\CTEBuilder\ExpressionBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @phpstan-import-type ResponseArray from DatatableResponse
 */
abstract class DatatableResult
{
    protected DatatableResponse $response;
    protected DatatableCriteriaInterface $criteriaClass;
    /**
     * @var ArrayCollection<array-key, ColumnField> $fields
     */
    protected ArrayCollection $fields;
    protected object $user;
    public DatatableRepositoryInterface $repository;
    /**
     * @var ArrayCollection<string, mixed> $options
     */
    public ArrayCollection $options;

    public function __construct(
        protected Request $request,
    ) {
        $this->response = new DatatableResponse();
    }

    /**
     * @return Collection<array-key, object>
     */
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
            /** @var array<array-key, object> $matches */
            $matches = $criteria->getQuery()
                ->getResult();
        } else {
            $matches = [];

            $records = $criteria instanceof ExpressionBuilder
                ? $criteria->execute()->fetchAllAssociative()
                : $criteria->fetchAllAssociative();

            foreach ($records as $record) {
                $matches[] = (object) $record;
            }
        }
        return new ArrayCollection($matches);
    }

    /**
     * @return ResponseArray
     */
    public function getResultSet(): array
    {
        $matches = $this->getMatches();

        $orderProperty = $this->criteriaClass->getDataOrderProperty();
        $orderDirection = $this->criteriaClass->getDataOrderDirection();
        $class = $this->criteriaClass->getTable()->getEntityClassName();

        // When sorting on a non-existing database field (dynamic column)
        if ($orderProperty && $orderDirection) {
            /** @var ArrayIterator<array-key, object> $iterator */
            $iterator = $matches->getIterator();
            $iterator->uasort(function ($a, $b) use ($orderProperty, $orderDirection) {

                $propertyAccessor = PropertyAccess::createPropertyAccessor();
                /** @var string $aValue */
                $aValue = $propertyAccessor->getValue($a, $orderProperty);
                /** @var string $bValue */
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

    public function attachFilters(ExpressionBuilder|QueryBuilder|DbalQueryBuilder$builder): void
    {
        $this->criteriaClass
            ->filter($builder)
            ->search($builder)
            ->order($builder)
            ->pagination($builder)
        ;
    }
}
