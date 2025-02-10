<?php

declare(strict_types=1);

namespace Artoroz\Datatable\Types\Field;

class ColumnField extends Field
{
    public string $title = '';
    /**
     * @var array<string, mixed>|string
     */
    public array|string $data = '';
    public bool $searchable = true;
    public bool $orderable = true;
    public bool $visible = true;
    public ?string $className = null;
    public mixed $transformer = null;
    public bool $raw = false;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(string $field, array $options)
    {
        parent::__construct($field);

        $this->parseOptions($options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function parseOptions(array $options): void
    {
        $this->queryField = $options['queryField'] ?? $this->queryField;
        $this->title = $options['title'] ?? '';
        $this->data = $options['data'] ?? $this->name;
        $this->searchable = $options['searchable'] ?? true;
        $this->orderable = $options['orderable'] ?? true;
        $this->visible = $options['visible'] ?? true;
        $this->className = $options['className'] ?? '';
        $this->transformer = $options['transformer'] ?? null;
        $this->raw = $options['raw'] ?? false;
    }

    /**
     * @return array{
     *     name: string,
     *     title: string,
     *     data: array<string, mixed>|string,
     *     searchable: bool,
     *     orderable: bool,
     *     visible: bool,
     *     className: ?string,
     * }
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'title' => $this->title,
            'data' => $this->data,
            'searchable' => $this->searchable,
            'orderable' => $this->orderable,
            'visible' => $this->visible,
            'className' => $this->className,
        ]);
    }

    public function parseField(object $entity): mixed
    {
        $entry = $this->getFromEntity($entity);
        if (is_callable($this->transformer)) {
            $entry = call_user_func($this->transformer, $entry, $entity);
        }
        if (! $this->raw) {
            $entry = htmlspecialchars($entry);
        }
        return $entry;
    }
}
