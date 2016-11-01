<?php
namespace AnnoncesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AnnoncesController extends Controller
{
	public function homeAction()
	{
		
		return $this->render('AnnoncesBundle:Annonces:home.html.twig');
	}
	
	public function addAction()
	{
		
		return $this->render('AnnoncesBundle:Annonces:add.html.twig');
	}
}
