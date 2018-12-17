<?php
namespace Artoroz\Datatable\Types\Field;

class UrlField extends ColumnField
{
    public $url_builder = null;

    public function parseOptions(array $options)
    {
        parent::parseOptions($options);
        $this->data = 'data_url';
        $this->name = 'data_url';
        $this->searchable = false;
        $this->orderable = false;
        $this->visible = false;
        $this->url_builder =  $options['url_builder'] ?? null;
    }

    public function parseField($row)
    {
        if (! is_callable($this->url_builder)) {
            return '';
        }

        return call_user_func($this->url_builder, $row);;
    }
}
