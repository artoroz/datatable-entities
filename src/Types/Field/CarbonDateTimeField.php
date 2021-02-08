<?php

namespace Artoroz\Datatable\Types\Field;

use Carbon\CarbonInterface;

class CarbonDateTimeField extends DateTimeField
{
    public function parseField($entity)
    {
        $entry = $this->getFromEntity($entity);

        if (! $entry instanceof CarbonInterface) {
            return [
                'display' => '-',
                'timestamp' => 0,
            ];
        }
        return [
            'display' => $entry->isoFormat('LLL'),
            'timestamp' => $entry->getTimestamp(),
        ];
    }
}
