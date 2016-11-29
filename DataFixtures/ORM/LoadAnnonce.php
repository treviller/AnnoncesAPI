<?php
namespace AnnoncesBundle\DataFixtures\ORM;

use AnnoncesBundle\Entity\Annonce;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

class LoadAnnonce extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 * @see \Doctrine\Common\DataFixtures\FixtureInterface::load()
	 */
	public function load(ObjectManager $manager) {
		
		$titles = ['Tirages Photos', 'Maison à vendre', 'Poissons rouges à adopter', 'Vends commode d\'occasion', 'Test'];
		$contents = ['Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
				Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
				Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 	
		Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'];
		$prix = [50, 0, 200, 30000, 5];
		$categories = [0,1,1,2,4];
		$villes = [0,1,2,3,3];
		
		$data = ['titles' => $titles, 'contents' => $contents, 'prix' => $prix, 'category_id' => $categories, 'ville_id' => $villes];
		
		for($i = 0 ; $i < 5; $i++)
		{
			$category = $this->getReference('category'.$data['category_id'][$i]);
			$ville = $this->getReference('ville'.$data['ville_id'][$i]);
			
			$annonce = new Annonce();
			$annonce->setTitle($data['titles'][$i]);
			$annonce->setContent($data['contents'][0]);
			$annonce->setPrix($data['prix'][$i]);
			$annonce->setCategory($category);
			$annonce->setCity($ville);
			
			$manager->persist($annonce);
		}
		
		$manager->flush();
	}

	public function getOrder()
	{
		return 4;
	}
}
