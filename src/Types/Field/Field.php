<?php
namespace Artoroz\Datatable\Types\Field;

use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class Field
{
    public $name = '';
    protected $accessor = '';

    public function __construct($field, array $options)
    {
        $this->accessor =  PropertyAccess::createPropertyAccessor();
        $this->name =  $field;
        $this->parseOptions($options);
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
        ];
    }

    protected function getFromEntity($entity)
    {
        return $this->accessor->getValue($entity, $this->name);
    }

}
