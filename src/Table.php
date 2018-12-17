<?php
namespace Artoroz\Datatable;

use Artoroz\Datatable\DatatableCriteriaInterface;
use Artoroz\Datatable\Response\DatatableResponse;
use Artoroz\Datatable\Types\DatatableResult;
use Artoroz\Datatable\Types\Field\Field;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;

abstract class Table extends DatatableResult
{
    /**
     * @var string $criteriaClassName
     */
    protected $criteriaClassName;

    /**
     * @var string $entityClassName
     */
    protected $entityClassName;

    /**
     * @var ArrayCollection $fields
     */
    protected $fields;

    /**
     * @var ArrayCollection $options
     */
    protected $options;

    /**
     * @var EntityRepository $repository
     */
    protected $repository;

    /**
     * @var DatatableCriteriaInterface $criteriaClass
     */
    protected $criteriaClass;


    public function __construct(DatatableResponse $response, Request $request, $options = [])
    {
        parent::__construct($response, $request);
        $this->fields = new ArrayCollection();
        $this->options = new ArrayCollection($options);
        $this->setUp();

        $this->criteriaClass = new $this->criteriaClassName($this->fields, $request);
    }

    public function setUp(): void
    {
    }

    public function add($field, $className, $options = []): Table
    {
        $field = new $className($field, $options);

        $this->fields->add($field);

        return $this;
    }

    public function getColumns(): array
    {
        // TODO extend to some adapter class?
        return $this->fields->map(
            function (Field $field) {
                return $field->toArray();
            }
        )->toArray();
    }

    public function setRepository(EntityRepository $repository): Table
    {
        $this->repository = $repository;
        return $this;
    }

    public function setDataStore(ArrayCollection $collection): Table
    {
        $this->repository = $collection;
        return $this;
    }
}
