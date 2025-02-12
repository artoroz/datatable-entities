<?php

declare(strict_types=1);

namespace Artoroz\Datatable\Response;

use Artoroz\Datatable\Types\Field\ColumnField;
use Doctrine\Common\Collections\Collection;

/**
 * @phpstan-type BaseResponseArray array{
 *     draw: int,
 *     recordsTotal: int,
 *     recordsFiltered: int,
 * }
 * @phpstan-type ResponseArray array{
 *     draw: int,
 *     recordsTotal: int,
 *     recordsFiltered: int,
 *     data: mixed,
 * }
 */
class DatatableResponse
{
    public int $draw = 0;
    /**
     * @var Collection<array-key, object> $entities
     */
    protected Collection $entities;
    public int $recordsTotal = 0;
    public int $recordsFiltered = 0;
    /**
     * @var Collection<array-key, ColumnField> $fields
     */
    private Collection $fields;

    /**
     * @return BaseResponseArray
     */
    protected function getBase(): array
    {
        return [
            'draw'            => $this->draw,
            'recordsTotal'    => $this->recordsTotal,
            'recordsFiltered' => $this->recordsFiltered,
        ];
    }

    /**
     * @return ResponseArray
     */
    public function getResponse(): array
    {
        return array_merge(
            $this->getBase(),
            [
                'data' => $this->getData()
            ]
        );
    }

    /**
     * @param Collection<array-key, object> $entities
     */
    public function setData(Collection $entities): void
    {
        $this->entities = $entities;
    }

    /**
     * @param Collection<array-key, ColumnField> $fields
     */
    public function setFields(Collection $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return array<array-key, array<string, mixed>>
     */
    public function getData(): array
    {
        // method transformer
        $fields = $this->fields;
        return $this->entities->map(function (object $entity) use ($fields): array {
            $row = [];
            foreach ($fields as $field) {
                $row[$field->name] = $field->parseField($entity);
            }
            return $row;
        })->toArray();
    }
}
