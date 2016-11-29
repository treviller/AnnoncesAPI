<?php
namespace AnnoncesBundle\DataFixtures\ORM;

use AnnoncesBundle\Entity\Ville;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

class LoadVille extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 * @see \Doctrine\Common\DataFixtures\FixtureInterface::load()
	 */
	public function load(ObjectManager $manager) {
		
		$names = ['Nantes', 'Paris', 'Bordeaux', 'Toulouse', 'Lyon'];
		
		for($i = 0; $i < 5; $i++)
		{
			$ville = new Ville();
			$ville->setName($names[$i]);
				
			$manager->persist($ville);
			$this->addReference('ville'.$i, $ville);
		}
		
		$manager->flush();

	}
	
	public function getOrder()
	{
		return 2;
	}

}
