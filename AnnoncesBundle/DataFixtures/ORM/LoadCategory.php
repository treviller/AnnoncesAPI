<?php
namespace AnnoncesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use AnnoncesBundle\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

class LoadCategory extends AbstractFixture implements OrderedFixtureInterface
{
	/**	
	 *
	 * {@inheritDoc}
	 * @see \Doctrine\Common\DataFixtures\FixtureInterface::load()
	 */
	public function load(ObjectManager $manager) {
		
		$names = ['Art', 'Immobilier', 'Meubles', 'Véhicule', 'Loisirs'];
		
		for($i = 0; $i <5; $i++)
		{
			$category = new Category();
			$category->setName($names[$i]);
			
			$manager->persist($category);
			$this->addReference('category'.$i, $category);
		}
		
		$manager->flush();
	}

	public function getOrder()
	{
		return 1;
	}
}
