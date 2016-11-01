<?php
namespace AnnoncesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AnnoncesBundle\Entity\Annonce;
use AnnoncesBundle\Form\Type\AnnonceType;
use AnnoncesBundle\Form\Type\CategoryType;
use AnnoncesBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Request;

class AnnoncesController extends Controller
{
	public function homeAction()
	{
		
		return $this->render('AnnoncesBundle:Annonces:home.html.twig');
	}
	
	public function addAction()
	{
		$annonce = new Annonce();
		
		$form = $this->get('form.factory')->create(AnnonceType::class, $annonce);
		
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
	
	public function addVilleAction()
	{
		
	}
}
