<?php
namespace Artoroz\Datatable;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

trait DatatableTrait
{
    protected abstract function getEntityManager(): EntityManagerInterface;
    protected abstract function getUser(): ?UserInterface;

    protected function createTable($tableClass, $entityClass, Request $request, $options = []): Table
    {
       /** @var EntityManagerInterface $em */
        $em = $options['em'] ?? $this->getEntityManager();

        if ($entityClass instanceof DatatableRepositoryInterface) {
            $repository = $entityClass;
        } else {
            $repository = $em->getRepository($entityClass);
        }

         return (new $tableClass($request, $this->getUser(), $options))
             ->setRepository($repository);
    }
}

