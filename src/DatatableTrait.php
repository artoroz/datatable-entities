<?php
namespace Artoroz\Datatable;

use Symfony\Component\HttpFoundation\Request;
use Artoroz\Datatable\Response\DatatableResponse;
use Artoroz\Datatable\Table;

trait DatatableTrait
{
    protected function createTable($tableClass, $entityClass, Request $request, $options = []): Table
    {
       // todo create factory for DI
        $em = isset($options['em']) ? $options['em'] : $this->getDoctrine()->getManager();

         $repository = $em->getRepository($entityClass);

         return (new $tableClass($request, $this->getUser(), $options))
             ->setRepository($repository);
    }
}
