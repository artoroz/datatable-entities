<?php
namespace Artoroz\Datatable\Response;

use Doctrine\Common\Collections\Collection;

class DatatableResponse
{
    /**
     * @var int $draw
     */
    public $draw;

    /**
     * @var Collection $entities
     */
    protected $entities;

    public $recordsTotal;

    public $recordsFiltered;

    /**
     * @var Collection $fields
     */
    private $fields;

    protected function getBase()
    {
        return [
            'draw'            => $this->draw,
            'recordsTotal'    => $this->recordsTotal,
            'recordsFiltered' => $this->recordsFiltered,
        ];
    }

    public function getResponse()
    {
        return array_merge(
            $this->getBase(),
            [
                'data' => $this->getData()
            ]
        );
    }
    public function setData(Collection $entities)
    {
        $this->entities = $entities;
    }
    public function setFields(Collection $fields)
    {
        $this->fields = $fields;
    }

    public function getData()
    {
        // method transformer
        $fields = $this->fields;
        return $this->entities->map(function ($entity) use ($fields) {
            $row = [];
            foreach ($fields as $field) {
                $row[$field->name] = $field->parseField($entity);
            }
            return $row;
        })->toArray();
    }
}
