<?php

namespace Artoroz\Datatable;

use Doctrine\Common\Collections\Criteria;

interface DatatableCriteriaInterface
{
    /**
     * @param Criteria $criteria
     *
     * @return DatatableCriteriaInterface
     */
    public function filter(Criteria $criteria): DatatableCriteriaInterface;

    /**
     * @param Criteria $criteria
     *
     * @return DatatableCriteriaInterface
     */
    public function search(Criteria $criteria): DatatableCriteriaInterface;

    /**
     * @param Criteria $criteria
     *
     * @return DatatableCriteriaInterface
     */
    public function order(Criteria $criteria): DatatableCriteriaInterface;

    /**
     * @return Criteria
     */
    public function createCriteria(array $options = []): Criteria;
}
