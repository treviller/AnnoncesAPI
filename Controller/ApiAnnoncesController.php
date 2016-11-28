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
use FOS\RestBundle\Controller\Annotations\FileParam;
use AnnoncesBundle\Entity\Photo;

class ApiAnnoncesController extends FOSRestController
{
	/**
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 * 
	 * @QueryParam(name="ville", requirements="[a-zA-Zéêàè-]+", strict=true, nullable=true)
	 * @QueryParam(name="category", requirements="[a-zA-Zéêèà-]+", strict=true, nullable=true)
	 * @QueryParam(name="page", requirements="\d+", strict=false, default=1)
	 * @Get("/annonces")
	 */
	public function getAnnoncesAction(ParamFetcher $paramFetcher)
	{
		$ville = $paramFetcher->get('ville');
		$category = $paramFetcher->get('category');
		$page = intval($paramFetcher->get('page'));
		
		$repository = $this->getDoctrine()->getManager()->getRepository('AnnoncesBundle:Annonce');
		
		if($category == null && $ville == null)
		{
			$view = $this->generateError('Le paramètre \'ville\' ou \'category\' doit être renseigné', 400);
		}
		else
		{
			$annonces = $repository->findAnnonces(array('city' => $ville, 'category' => $category), true);

			if($annonces == null)
			{
				$view = $this->view(array('Aucune annonce trouvée'), 200);
			}
			else
			{
				
				foreach($annonces as $annonce)
				{
					$this->setFullUrlPhotos($annonce->getPhotos());
				}
				$resultat = array('page' => $page,
								'count' => count($annonces), 
								'annonces' => $annonces);
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
			$view = $this->generateError('Annonce introuvable', 404);
		}
		else
		{
			$this->setFullUrlPhotos($annonce->getPhotos());
			$resultat = array('annonce' => $annonce);
			$view = $this->view($resultat, 200);
		}
	
		return $this->handleView($view);
	}
	
	/**
	 * @RequestParam(name="title", requirements=".+")
	 * @RequestParam(name="content", requirements=".+")
	 * @RequestParam(name="prix", requirements="\d+", strict=false)
	 * @RequestParam(name="category", strict=false, requirements="[a-zA-Zéêèà-]+")
	 * @RequestParam(name="id_category", strict=false, requirements="\d+", default=null)
	 * @RequestParam(name="city", strict=false, requirements="[a-zA-Zéêèà-]+")
	 * @RequestParam(name="id_city", strict=false, requirements="\d+")
	 * @RequestParam(name="id_photo_1", strict=false, requirements="\d+", default=null)
	 * @RequestParam(name="id_photo_2", strict=false, requirements="\d+", default=null)
	 * @RequestParam(name="id_photo_3", strict=false, requirements="\d+", default=null)
	 * @Post("/annonces")
	 */
	public function postAnnoncesAction(ParamFetcher $paramFetcher)
	{
		$annonce = new Annonce();
		
		$parameters = $paramFetcher->all();
		
		$view = $this->handleAnnonce($annonce, $parameters);
		
		return $this->handleView($view);
	}
	
	/**
	 * @RequestParam(name="title", requirements=".+")
	 * @RequestParam(name="content", requirements=".+")
	 * @RequestParam(name="prix", requirements="\d+", strict=false)
	 * @RequestParam(name="category", strict=false, requirements="[a-zA-Zéêèà-]+")
	 * @RequestParam(name="id_category", strict=false, requirements="\d+", default=null)
	 * @RequestParam(name="city", strict=false, requirements="[a-zA-Zéêèà-]+")
	 * @RequestParam(name="id_city", strict=false, requirements="\d+")
	 * @RequestParam(name="id_photo_1", strict=false, requirements="\d+", default=null)
	 * @RequestParam(name="id_photo_2", strict=false, requirements="\d+", default=null)
	 * @RequestParam(name="id_photo_3", strict=false, requirements="\d+", default=null)
	 * @Put("/annonces/{id}", requirements={"id" = "\d+"})
	 */
	public function putAnnonceAction(ParamFetcher $paramFetcher, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$annonce = $em->getRepository('AnnoncesBundle:Annonce')->findAnnonceWithCatAndPhotos($id);
		
		if($annonce == null)
		{
			$view = $this->generateError('Annonce introuvable', 404);
		}
		else
		{
			$parameters = $paramFetcher->all();
			
			//On enlève les anciennes photos
			if(isset($parameters['id_photo_1']) || isset($parameters['id_photo_2']) || isset($parameters['id_photo_3']))
			{
				if(!$annonce->getPhotos()->isEmpty())
				{
					foreach($annonce->getPhotos() as $photo)
					{
						$annonce->removePhoto($photo);
						if($photo->getId() !== $parameters['id_photo_1'] && $photo->getId() !== $parameters['id_photo_2'] && $photo->getId() !== $parameters['id_photo_3'])
						{
							var_dump($photo);
							//$em->remove($photo);
						}
						else
							$em->persist($photo);
					}
					$em->flush();
				}
			}
			
			$view = $this->handleAnnonce($annonce, $parameters);
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
			$view = $this->generateError('Annonce introuvable', 404);
		}
		else
		{
			$em->remove($annonce);
			$view = $this->view('', 204);
		}
		return $this->handleView($view);
	}
	
	protected function generateError($message, $code, $args='')
	{
		$error = array(
				'code' => $code,
				'details' => $args,
				'message' => $message
		);
		$resultat = array('error' => $error);
		return $this->view($resultat, $code);
	}
	
	protected function setFullUrlPhotos($photos)
	{
		foreach($photos as $photo)
		{
			$photo->setUrl($photo->getUploadDir().'/'.$photo->getId().'.'.$photo->getUrl());
		}
	}
	
	protected function handleAnnonce($annonce, $parameters)
	{
		$em = $this->getDoctrine()->getManager();
		//Gestion de la catégorie
		if($parameters['id_category'] != null)
		{
			$parameters['category'] = $em->getRepository('AnnoncesBundle:Category')->findOneBy(array('id' => $parameters['id_category']));
			unset($parameters['id_category']);
		}
		elseif($parameters['category'] != null)
		$parameters['category'] = $em->getRepository('AnnoncesBundle:Category')->findOneBy(array('name' => $parameters['category']));
		else
			return $this->generateError('Aucune catégorie n\'a été spécifiée.', 400);
		
		//Gestion de la ville
		if($parameters['id_city'] != null)
		{
			$parameters['city'] = $em->getRepository('AnnoncesBundle:Ville')->findOneBy(array('id' => $parameters['id_city']));
			unset($parameters['id_city']);
		}
		elseif($parameters['city'] != null)
		{
			$parameters['city'] = $em->getRepository('AnnoncesBundle:Ville')->findOneBy(array('name' => $parameters['city']));
		}
		else
			return $this->generateError('Aucune ville n\' a été spécifiée.', 400);
				
		//Gestion des photos
		for($i = 1 ; $i < 4 ; $i++)
		{
			if($parameters['id_photo_'.$i] != null)
			{
				$photo = $em->getRepository('AnnoncesBundle:Photo')->findOneBy(array('id' => intval($parameters['id_photo_'.$i])));

				if($photo === null)
				{
					return $this->generateError('Cette photo est introuvable', 404, array('id' => $parameters['id_photo_'.$i]));
				}
				if($photo->getAnnonce() !== null)
				{
					return $this->generateError('Cette photo est déjà utilisée dans une autre annonce.', 400, array('id' => $parameters['id_photo_'.$i]));
				}
				$annonce->addPhoto($photo);
				unset($parameters['id_photo_'.$i]);
			}
		}

		//Gestion du reste des données
		$annonce->hydrate($parameters);

		$em->persist($annonce);
		$em->flush();

		return $this->view('', 200);
	}
}
