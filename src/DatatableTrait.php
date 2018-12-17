<?php
namespace Artoroz\Datatable;

use Symfony\Component\HttpFoundation\Request;
use Artoroz\Datatable\Response\DatatableResponse;
use Artoroz\Datatable\Table;

trait DatatableTrait
{
    protected function createTable($class, Request $request, $options): Table
    {
       // todo create factory for DI
         $datatableResponse = new DatatableResponse();

         return new $class($datatableResponse, $request, $options);
    }
}
