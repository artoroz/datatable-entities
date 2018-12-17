<?php
namespace Artoroz\Datatable\Types\Field;

class DateField extends ColumnField
{
    public function parseOptions(array $options)
    {
        parent::parseOptions($options);

        $this->data = [
            '_' => $this->name . '.display',
            'sort' => $this->name . '.timestamp'
        ];
    }
    public function parseField($entity)
    {
        $entry = $this->getFromEntity($entity);
        if (!$entry instanceof \DateTime) {
            return [
                'display' => '-',
                'timestamp' => 0
            ];
        }

        return [
            'display' => $entry->format('d-m-Y'),
            'timestamp' => $entry->getTimestamp()
        ];
    }
}
