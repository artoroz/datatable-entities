<?php

declare(strict_types=1);

namespace Artoroz\Datatable\Types\Field;

/**
 * @phpstan-import-type OptionsArray from ColumnField
 */
class UrlField extends ColumnField
{
    public mixed $url_builder = null;

    /**
     * @param array{url_builder?: mixed}&OptionsArray $options
     */
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
