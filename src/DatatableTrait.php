<?php
namespace Artoroz\Datatable;

use Symfony\Component\HttpFoundation\Request;

trait DatatableTrait
{
    protected function createTable($tableClass, $entityClass, Request $request, $options = []): Table
    {
        $em = isset($options['em']) ? $options['em'] : $this->getDoctrine()->getManager();

        if ($entityClass instanceof DatatableRepositoryInterface) {
            $repository = $entityClass;
        } else {
            $repository = $em->getRepository($entityClass);
        }

         return (new $tableClass($request, $this->getUser(), $options))
             ->setRepository($repository);
    }
}
