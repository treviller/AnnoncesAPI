<?php
namespace AnnoncesBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use AnnoncesBundle\Entity\Category;
use FOS\RestBundle\Controller\Annotations\Get;

class ApiCategoriesController extends FOSRestController
{
	/**
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @RequestParam(name="name", requirements="[a-zA-Zéêèà-]+")
	 * @Post("/categories")
	 */
	public function postCategoriesAction(ParamFetcher $paramFetcher)
	{
		$name = $paramFetcher->get('name');
		
		$em = $this->getDoctrine()->getManager();
		
		if($em->getRepository('AnnoncesBundle:Category')->findOneBy(array('name' => $name)) != null)
		{
			$error = array(
				'code' => 409,
				'message' => 'Cette catégorie existe déjà'
			);
			$resultat = array('error' => $error);
			$view = $this->view($resultat, 409);
		}
		else
		{
			$category = new Category();
			$category->setName($name);
			
			$em->persist($category);
			$em->flush();
			
			$view = $this->view("", 204);
		}
		
		return $this->handleView($view);
	}
	
	/**
	 * @Get("/categories")
	 */
	public function getCategoriesAction()
	{
		$categories = $this->getDoctrine()->getManager()->getRepository('AnnoncesBundle:Category')->findAll();
		
		if($categories == null)
		{
			$resultat = array('Il n\'y a aucune catégorie enregistrée.');
		}
		else
		{
			$resultat = array('categories' => $categories);
		}
		
		return $this->handleView($this->view($resultat, 200));
	}
}
