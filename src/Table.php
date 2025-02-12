<?php

declare(strict_types=1);

namespace Artoroz\Datatable;

use Artoroz\Datatable\Types\DatatableResult;
use Artoroz\Datatable\Types\Field\ColumnField;
use Artoroz\Datatable\Types\Field\Field;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

/**
 * @phpstan-import-type OptionsArray from ColumnField
 */
abstract class Table extends DatatableResult
{
    /**
     * @var class-string<DatatableCriteriaInterface> $criteriaClassName
     */
    protected string $criteriaClassName;
    /**
     * @var class-string<object>|null $entityClassName
     */
    protected ?string $entityClassName = null;
    protected DatatableCriteriaInterface $criteriaClass;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(Request $request, object $user, array $options = [])
    {
        parent::__construct($request);
        $draw = $request->get('draw');
        $this->response->draw = is_numeric($draw) ? (int) $draw : 0;
        $this->fields = new ArrayCollection();
        $this->user = $user;
        $this->options = new ArrayCollection($options);
        $this->setUp();
        $this->criteriaClass = new $this->criteriaClassName($this, $request);
    }

    public function setUp(): void
    {
    }

    public function setRepository(DatatableRepositoryInterface $repository): Table
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @param class-string<ColumnField> $className
     * @param OptionsArray $options
     */
    public function add(string $fieldName, string $className, array $options = []): Table
    {
        $field = new $className($fieldName, $options);
        $this->fields->add($field);
        return $this;
    }

    public function get(int|string $fieldName): ?ColumnField
    {
        return $this->fields->get($fieldName);
    }

    /**
     * @return array<array-key, array<string, mixed>>
     */
    public function getColumns(): array
    {
        // TODO extend to some adapter class?
        return $this->fields->map(
            function (Field $field) {
                return $field->toArray();
            }
        )->toArray();
    }

    /**
     * @return class-string<object>|null
     */
    public function getEntityClassName(): ?string
    {
        return $this->entityClassName;
    }
}
