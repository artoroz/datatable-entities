<?php

declare(strict_types=1);

namespace Artoroz\Datatable\Types\Field;

class DropdownField extends ColumnField
{
    public mixed $actions = null;

    const BOOTSTRAP_ACTIONS_TEMPLATE = <<<BOOTSTRAP_ACTIONS_TEMPLATE
        <div class="btn-group table-actions">
            <button type="button" class="btn btn-default btn-icon dropdown-toggle" data-toggle="dropdown">
                <span class="mi mi-more_vert"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <actions/>
            </ul>
        </div>
BOOTSTRAP_ACTIONS_TEMPLATE;

    public function parseOptions(array $options): void
    {
        parent::parseOptions($options);

        $this->data = 'dropdown';
        $this->actions = $options['actions'] ?? [];
        $this->searchable = false;
        $this->orderable = false;
    }

    public function parseField(object $entity): mixed
    {
        if (is_callable($this->actions)) {
            return call_user_func($this->actions, $entity);
        }
        return parent::parseField($entity);

    }
}
