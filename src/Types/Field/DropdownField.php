<?php
namespace Artoroz\Datatable\Types\Field;

class DropdownField extends ColumnField
{
    public $actions = [];

    public function parseOptions(array $options)
    {
        parent::parseOptions($options);

        $this->data = 'dropdown';
        $this->actions = $options['actions'] ?? [];
        $this->searchable = false;
        $this->orderable = false;
    }

    public function parseField($entity)
    {
        if (is_callable($this->actions)) {
            return call_user_func($this->actions, $entity);
        }
        return $entry;

    }
}
