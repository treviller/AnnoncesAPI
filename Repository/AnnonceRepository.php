<?php

namespace AnnoncesBundle\Repository;

/**
 * AnnonceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AnnonceRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAnnonces($category, $city)
	{
		$query = $this->createQueryBuilder('a');
		
		$query
			->innerJoin('a.category', 'c')
			->addSelect('c')
			->where('c.name = :category')
			->setParameter('category', $category)
			->andWhere('a.city = :city')
			->setParameter('city', $city);
		
		return $query->getQuery()->getArrayResult();
	}
	
}
