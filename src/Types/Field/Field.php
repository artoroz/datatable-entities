<?php
namespace Artoroz\Datatable\Types\Field;

use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class Field
{
    public $name = '';
    public $queryField = '';
    protected $accessor = '';

    public function __construct($field)
    {
        $this->accessor =  PropertyAccess::createPropertyAccessor();
        $this->name =  $field;
        $this->queryField =  $field;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
        ];
    }

    protected function getFromEntity($entity)
    {
        return $this->accessor->getValue($entity, $this->queryField);
    }

}
