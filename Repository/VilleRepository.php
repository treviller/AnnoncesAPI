<?php

namespace AnnoncesBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * VilleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VilleRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllWithPage($page)
	{
		$query = $this->createQueryBuilder('v')
		->getQuery()
		->setFirstResult(($page - 1) * 10)
		->setMaxResults(10);
		
		return new Paginator($query);
	}
}
