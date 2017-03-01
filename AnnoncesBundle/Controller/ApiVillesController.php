<?php
namespace AnnoncesBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;

class ApiVillesController extends FOSRestController
{	
	/**
	 * @QueryParam(name="page", requirements="\d+", strict=false, default=1)
	 * @Get("/villes")
	 */
	public function getVillesAction(ParamFetcher $paramFetcher)
	{
		$page = intval($paramFetcher->get('page'));
		$villes = $this->getDoctrine()->getManager()->getRepository('AnnoncesBundle:Ville')->findAllWithPage($page);
		
		if($villes === null)
		{
			$resultat = array('Il n\'y a aucune ville enregistrée');
		}
		else 
		{
			$listVilles = array();
			
			foreach($villes as $ville)
			{
				$listVilles[] = $ville;
			}
			$resultat = array('page' => $page,
					'total_per_page' => 10,
					'count' => count($villes),
					'categories' => $listVilles);
		}
		
		return $this->handleView($this->view($resultat, 200));
	}
}
