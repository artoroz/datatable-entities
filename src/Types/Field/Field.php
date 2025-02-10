<?php

declare(strict_types=1);

namespace Artoroz\Datatable\Types\Field;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class Field
{
    public string $name = '';
    public string $queryField = '';
    protected PropertyAccessor $accessor;

    public function __construct(string $field)
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->name =  $field;
        $this->queryField =  $field;
    }

    /**
     * @return array{name:string}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }

    protected function getFromEntity(object $entity): mixed
    {
        return $this->accessor->getValue($entity, $this->queryField);
    }

}
