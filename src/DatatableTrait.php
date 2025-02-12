<?php

declare(strict_types=1);

namespace Artoroz\Datatable;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

trait DatatableTrait
{
    abstract protected function getDoctrine(): ManagerRegistry;

    /**
     * @return UserInterface|null
     */
    abstract protected function getUser();

    /**
     * @param class-string<Table> $tableClass
     * @param class-string<object>|DatatableRepositoryInterface $entityClass
     * @param array<string, mixed> $options
     */
    protected function createTable(string $tableClass, string|DatatableRepositoryInterface $entityClass, Request $request, array $options = []): Table
    {
        $em = $options['em'] ?? $this->getDoctrine()->getManager();

        if ($entityClass instanceof DatatableRepositoryInterface) {
            $repository = $entityClass;
        } else {
            $repository = $em->getRepository($entityClass);
        }

         return (new $tableClass($request, $this->getUser(), $options))
             ->setRepository($repository);
    }
}
