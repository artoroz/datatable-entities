<?php
namespace Artoroz\Datatable;

use Artoroz\Datatable\DatatableCriteriaInterface;
use Artoroz\Datatable\Response\DatatableResponse;
use Artoroz\Datatable\Types\DatatableResult;
use Artoroz\Datatable\Types\Field\Field;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;

abstract class Table extends DatatableResult
{
    /**
     * @var string $criteriaClassName
     */
    protected $criteriaClassName;

    /**
     * @var ArrayCollection $fields
     */
    protected $fields;

    /**
     * @var object $user
     */
    protected $user;

    /**
     * @var DatatableRepositoryInterface $repository
     */
    public $repository;

    /**
     * @var ArrayCollection $options
     */
    protected $options;

    /**
     * @var DatatableCriteriaInterface $criteriaClass
     */
    protected $criteriaClass;


    public function __construct(Request $request, $user, $options = [])
    {
        parent::__construct($this, $request);
        $this->response->draw = (int)$request->get('draw');
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

    public function add($fieldName, $className, $options = []): Table
    {
        $field = new $className($fieldName, $options);
        $this->fields->add($field);
        return $this;
    }
    public function get($fieldName): ?Field
    {
        return $this->fields->get($fieldName);
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
}
