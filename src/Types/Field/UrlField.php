<?php

declare(strict_types=1);

namespace Artoroz\Datatable\Types\Field;

class UrlField extends ColumnField
{
    public mixed $url_builder = null;

    public function parseOptions(array $options): void
    {
        parent::parseOptions($options);
        $this->data = 'data_url';
        $this->name = 'data_url';
        $this->searchable = false;
        $this->orderable = false;
        $this->visible = false;
        $this->url_builder =  $options['url_builder'] ?? null;
    }

    public function parseField(object $entity): mixed
    {
        if (! is_callable($this->url_builder)) {
            return '';
        }

        return call_user_func($this->url_builder, $entity);
    }
}
