<?php
namespace AnnoncesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AnnoncesBundle\Entity\Annonce;
use AnnoncesBundle\Form\Type\AnnonceType;
use AnnoncesBundle\Form\Type\CategoryType;
use AnnoncesBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AnnoncesBundle\Entity\Photo;
use Doctrine\Common\Collections\ArrayCollection;
use AnnoncesBundle\Entity\Ville;

class AnnoncesController extends Controller
{
	public function homeAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$annonces = array();
		$categories = $em->getRepository('AnnoncesBundle:Category')->findAll();
		
		if($request->get('city') != "" && !$this->get('annonces.geonamesapi')->checkCity($request->get('city')))
			$request->getSession()->getFlashbag()->add('danger', 'Erreur : la ville spécifiée est introuvable.');
		elseif($request->isMethod('post') && ($request->get('category') != "" || $request->get('city') != ""))
		{	
			$annonces = $em->getRepository('AnnoncesBundle:Annonce')->findAnnonces(array('category' => $request->get('category'), 'city' => $request->get('city')));
		}
		
		return $this->render('AnnoncesBundle:Annonces:home.html.twig', array('categories' => $categories, 'annonces' => $annonces));
	}
	
	public function addAction(Request $request)
	{
		$annonce = new Annonce();
		$annonce->addPhoto(new Photo());
		$form = $this->get('form.factory')->create(AnnonceType::class, $annonce);
		
		if($request->isMethod("post") && $form->handleRequest($request)->isValid())
		{
			$this->handleAnnonce($annonce);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($annonce);
			$em->flush();
				
			$request->getSession()->getFlashBag()->add('info', 'L\'annonce a bien été ajoutée.');		
			return $this->redirectToRoute('annonces_view', array('id' => $annonce->getId()));
		}
		
		return $this->render('AnnoncesBundle:Annonces:add.html.twig', array('form' => $form->createView()));
	}
	
	public function addCategoryAction(Request $request)
	{
		$category = new Category();
		$form = $this->get('form.factory')->create(CategoryType::class, $category);
		
		if($request->isMethod("post") && $form->handleRequest($request)->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->persist($category);
			$em->flush();
			
			$request->getSession()->getFlashBag()->add('info', 'La catégorie a bien été ajoutée.');
		}
		
		return $this->render('AnnoncesBundle:Annonces:addCategory.html.twig', array('form' => $form->createView()));
	}
	
	public function viewAction($id)
	{
		$annonce = $this->getDoctrine()->getManager()->getRepository('AnnoncesBundle:Annonce')->findAnnonceWithCatAndPhotos($id);
		
		if($annonce === null)
		{
			throw new NotFoundHttpException('L\'annonce spécifiée est introuvable.');
		}
		
		return $this->render('AnnoncesBundle:Annonces:view.html.twig', array('annonce' => $annonce));
	}
	
	public function editAction($id, Request $request)
	{
		//Initialisation
		$em = $this->getDoctrine()->getManager();
		$annonce = $em->getRepository('AnnoncesBundle:Annonce')->findAnnonceWithCatAndPhotos($id);
		
		if($annonce === null)
			throw new NotFoundHttpException('L\'annonce spécifiée est introuvable.');
		
		$oldCity = $annonce->getCity();
		$annonce->setCity($annonce->getCity()->getName());
		
		$photos = new ArrayCollection();
		foreach($annonce->getPhotos() as $photo)
		{
			$photo->defineFileAfterLoad();
			$photos->add($photo);
		}

		$form = $this->get('form.factory')->create(AnnonceType::class, $annonce);
		
		//Check and perform
		if($request->isMethod('post') && $form->handleRequest($request)->isValid())
		{
			
			foreach($photos as $photo)
			{
				//On vérifie que la photo n'est plus dans le formulaire, ou que ses attributs ne sont pas null
				if($photo->getFile() === null && $photo->getUrl() === null)
				{
					$annonce->removePhoto($photo);
				}
				if($annonce->getPhotos()->contains($photo) === false)
				{
					$photo->setAnnonce(null);
					$em->remove($photo);
				}
			}
			
			$this->handleAnnonce($annonce);
			
			$em->persist($annonce);
			$em->flush();
			
			if($oldCity !== $annonce->getCity())
			{
				$this->checkCityInDB($oldCity);
			}
			
			$request->getSession()->getFlashBag()->add('info', 'L\'annonce a bien été modifiée.');
			return $this->redirectToRoute('annonces_view', array('id' => $id));
		}
		
		return $this->render('AnnoncesBundle:Annonces:edit.html.twig', array('annonce' => $annonce, 'form' => $form->createView()));
	}
	
	public function deleteAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$annonce = $em->getRepository('AnnoncesBundle:Annonce')->findOneBy(array('id' => $id));
		
		if($annonce === null)
			throw new NotFoundHttpException('L\'annonce spécifiée est introuvable.');
		
		$city = $annonce->getCity();
			
		$em->remove($annonce);
		$em->flush();
		
		$this->checkCityInDB($city);
		
		$request->getSession()->getFlashBag()->add('info', 'L\'annonce a bien été supprimée.');
		return $this->redirectToRoute('annonces_home');
	}
	
	//A utiliser plus tard sur edit
	protected function handleAnnonce($annonce)
	{
		$em = $this->getDoctrine()->getManager();
		//Gestion des photos
		foreach($annonce->getPhotos() as $photo)
		{
			if($photo->getFile() == null)
			{
				$annonce->removePhoto($photo);
			}
		}
			
		//Gestion de la ville
		$city = $em->getRepository('AnnoncesBundle:Ville')->findOneBy(array('name' => $annonce->getCity()));
		if($city === null)
		{
			$city = new Ville();
			$city->setName($annonce->getCity());
		}
		$annonce->setCity($city);
	}
	
	protected function checkCityInDB($city)
	{
		$em = $this->getDoctrine()->getManager();
		$annoncesRestantes = $em->getRepository('AnnoncesBundle:Annonce')->findAnnonces(array('city' => $city->getName()));

		if(empty($annoncesRestantes))
		{
			$em->remove($city);
			$em->flush();
		}
	}
}
