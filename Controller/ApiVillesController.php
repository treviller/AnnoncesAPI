<?php
namespace AnnoncesBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;

class ApiVillesController extends FOSRestController
{
	/**
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @RequestParam(name="name", requirements="[a-zA-Zéèêà-]+")
	 * @Post("/villes")
	 */
	public function postVillesAction(ParamFetcher $paramFetcher)
	{
		$view = $this->view(array($paramFetcher->get('name')), 204);//A modifier selon nécessaire ou pas
		
		return $this->handleView($view);
	}
}
