<?php

namespace AnnoncesBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllWithPage($page)
	{
		$query = $this->createQueryBuilder('c')
					  ->getQuery()
					  ->setFirstResult(($page - 1) * 10)
					  ->setMaxResults(10);
		
		return new Paginator($query);
	}
}
