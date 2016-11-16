<?php
namespace AnnoncesBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use AnnoncesBundle\Entity\Annonce;

class ApiAnnoncesController extends FOSRestController
{
	/**
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 * 
	 * @QueryParam(name="ville", requirements="[a-zA-Zéêàè-]+", strict=true, nullable=true)
	 * @QueryParam(name="category", requirements="[a-zA-Zéêèà-]+", strict=true, nullable=true)
	 * @Get("/annonces")
	 */
	public function getAnnoncesAction(ParamFetcher $paramFetcher)
	{
		$ville = $paramFetcher->get('ville');
		$category = $paramFetcher->get('category');
		
		$repository = $this->getDoctrine()->getManager()->getRepository('AnnoncesBundle:Annonce');
		
		if($category == null && $ville == null)
		{
			$error = array(
					'code' => 400,
					'message' => 'Le paramètre \'ville\' ou \'category\' doit être renseigné'
			);
			$data = array('error' => $error);
			
			$view = $this->view($data, 400);
		}
		else
		{
			if($category == null)
			{
				$annonces = $repository->findAnnoncesByCityWithPhotos($ville);
			
			}
			elseif($ville == null)
			{
				$annonces  = $repository->findAnnoncesByCategoryWithPhotos($category);
			}
			else 
			{
				$annonces = $repository->findAnnoncesWithPhotos($ville, $category);
			}
			
			if($annonces == null)
			{
				$error = array(
						'code' => 404,
						'message' => 'Annonce introuvable'
				);
				$resultat = array('error' => $error);
				$view = $this->view($resultat, 404);
			}
			else
			{
				$resultat = array('annonces' => $annonces);
				$view = $this->view($resultat, 200);
			}
			
		}
	
		return $this->handleView($view);
	}
	
	/**
	 * 
	 * @param integer $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 * 
	 * @Get("/annonces/{id}", requirements={"id" = "\d+"})
	 */
	public function getAnnonceAction($id)
	{
		$annonce = $this->getDoctrine()->getManager()->getRepository('AnnoncesBundle:Annonce')->findAnnonceWithCatAndPhotos($id);
		
		if(null == $annonce)
		{
			$error = array(
					'code' => 404,
					'message' => 'Annonce introuvable'
			);
			$resultat = array('error' => $error);
			$view = $this->view($resultat, 404);
		}
		else
		{
			/*foreach($annonce->getPhotos() as $photo) Est ce qu'on rajoute le fichier à l'envoi ?
			{
				$photo->defineFileAfterLoad();
			}*/
			$resultat = array('annonce' => $annonce);
			$view = $this->view($resultat, 200);
		}
	
		return $this->handleView($view);
	}
	
	/**
	 * @RequestParam(name="title", requirements=".+")
	 * @RequestParam(name="content", requirements=".+")
	 * @RequestParam(name="prix", requirements="\d+", strict=false)
	 * @RequestParam(name="category")
	 * @RequestParam(name="city", requirements="[a-zA-Zéêèà-]+")
	 * @RequestParam(name="photos", strict=false, default=null)
	 * @Post("/annonces")
	 */
	public function postAnnoncesAction(ParamFetcher $paramFetcher)
	{
		$annonce = new Annonce();
		$em = $this->getDoctrine()->getManager();
		
		$parameters = $paramFetcher->all();
		
		$parameters['category'] = $em->getRepository('AnnoncesBundle:Category')->findOneBy($parameters['category']);
		
		$annonce->hydrate($parameters);
		
		$em->persist($annonce);
		$em->flush();
		
		$view = $this->view('', 204);
		return $this->handleView($view);
	}
	
	/**
	 * @RequestParam(name="title", requirements=".+", default=null, strict=false)
	 * @RequestParam(name="content", requirements=".+", default=null, strict=false)
	 * @RequestParam(name="prix", requirements="\d+", default=null, strict=false)
	 * @RequestParam(name="category", default=null, strict=false)
	 * @RequestParam(name="city", requirements="[a-zA-Zéêèà-]+", default=null, strict=false)
	 * @RequestParam(name="photos", strict=false, default=null)
	 * @Put("/annonces/{id}", requirements={"id" = "\d+"})
	 */
	public function putAnnonceAction(ParamFetcher $paramFetcher, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$annonce = $em->getRepository('AnnoncesBundle:Annonce')->findAnnonceWithCatAndPhotos($id);
		
		if($annonce == null)
		{
			$error = array(
					'code' => 404,
					'message' => 'Annonce introuvable'
			);
			$resultat = array('error' => $error);
			$view = $this->view($resultat, 404);
		}
		else
		{
			$parameters = $paramFetcher->all();
			
			if($parameters['category'] != null)
				$parameters['category'] = $em->getRepository('AnnoncesBundle:Category')->findOneBy($parameters['category']);
			
			foreach($parameters as $propertyName => $value)
			{
				if($parameters[$propertyName] == null)
					unset($parameters[$propertyName]);
			}
		
			$annonce->hydrate($parameters);
		
			$em->persist($annonce);
			$em->flush();
		
			$view = $this->view('', 204);
		}
		return $this->handleView($view);
	}
	
	/**
	 * 
	 * @param integer $id
	 * @Delete("/annonces/{id}", requirements={"id" = "\d+"})
	 */
	public function deleteAnnonceAction($id)
	{
		$em =  $this->getDoctrine()->getManager();
		$annonce = $em->getRepository('AnnoncesBundle:Annonce')->findAnnonceWithCatAndPhotos($id);
		
		if(null == $annonce)
		{
			$error = array(
					'code' => 404,
					'message' => 'Annonce introuvable'
			);
			$resultat = array('error' => $error);
			$view = $this->view($resultat, 404);
		}
		else
		{
			$em->remove($annonce);
			$resultat = array('Annonce '.$id.' bien supprimée.');
			$view = $this->view($resultat, 200);
		}
		return $this->handleView($view);
	}

}
