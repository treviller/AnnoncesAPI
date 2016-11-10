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
use Symfony\Component\HttpFoundation\File\File;
use AnnoncesBundle\Form\Type\PhotoType;
use Doctrine\Common\Collections\ArrayCollection;

class AnnoncesController extends Controller
{
	public function homeAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		
		$categories = $em->getRepository('AnnoncesBundle:Category')->findAll();
		$annonces = array();
		
		if($request->isMethod('post') && ($request->get('category') != "" || $request->get('city') != ""))
		{	
			if($request->get('category') == "")
				$annonces = $em->getRepository('AnnoncesBundle:Annonce')->findAnnoncesByCity($request->get('city'));
			elseif($request->get('city') == "")
				$annonces = $em->getRepository('AnnoncesBundle:Annonce')->findAnnoncesByCategory($request->get('category'));
			else
				$annonces = $em->getRepository('AnnoncesBundle:Annonce')->findAnnonces($request->get('category'), $request->get('city'));	
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
			$em = $this->getDoctrine()->getManager();
			
			$em->persist($annonce);
			$em->flush();
				
			$request->getSession()->getFlashBag()->add('info', 'L\'annonce a bien été ajoutée.');
				
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
		$em = $this->getDoctrine()->getManager();
		$annonce = $em->getRepository('AnnoncesBundle:Annonce')->findAnnonceWithCatAndPhotos($id);
		
		$photos = new ArrayCollection();
		
		foreach($annonce->getPhotos() as $photo)
		{
			$photos->add($photo);
			$photo->defineFileAfterLoad();
		}
		
		$form = $this->get('form.factory')->create(AnnonceType::class, $annonce);
		
		if($request->isMethod('post') && $form->handleRequest($request)->isValid())
		{
			foreach($photos as $photo)
			{
				if(false === $annonce->getPhotos()->contains($photo))
				{
					$photo->setAnnonce(false);
					$em->remove($photo);
				}
			}
			
			$em->persist($annonce);
			$em->flush();
			
			$request->getSession()->getFlashBag()->add('info', 'L\'annonce a bien été modifiée.');
			return $this->redirectToRoute('annonces_view', array('id' => $id));
		}
		
		if($annonce === null)
			throw new NotFoundHttpException('L\'annonce spécifiée est introuvable.');
		
		return $this->render('AnnoncesBundle:Annonces:edit.html.twig', array('annonce' => $annonce, 'form' => $form->createView()));
	}
	
	public function deleteAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$annonce = $em->getRepository('AnnoncesBundle:Annonce')->findOneBy(array('id' => $id));
		
		$em->remove($annonce);
		$em->flush();
		
		$request->getSession()->getFlashBag()->add('info', 'L\'annonce a bien été supprimée.');
		return $this->redirectToRoute('annonces_home');
	}
}
