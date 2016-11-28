<?php
namespace AnnoncesBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\FileParam;
use AnnoncesBundle\Entity\Photo;

class ApiPhotosController extends FOSRestController
{
	/**
	 * @FileParam(name="photo1", image=true, default=null)
	 * @FileParam(name="photo2", strict=false, image=true, default=null)
	 * @FileParam(name="photo3", strict=false, image=true, default=null)
	 * @Post("/photos")
	 */
	public function postPhotosAction(ParamFetcher $paramFetcher)
	{
		$em = $this->getDoctrine()->getManager();
		$photos = [];
		
		for($i = 1; $i < 4; $i++)
		{
			if($paramFetcher->get('photo'.$i) != null)
			{
				$photo = new Photo();
				$dateExpiration = new \Datetime();
				$dateExpiration->add(new \DateInterval('PT15M'));
				$photo->setFile($paramFetcher->get('photo'.$i));
				$photo->setExpiredAt($dateExpiration);
				$em->persist($photo);
				$photos[] = $photo;
			}
		}
		
		$em->flush();
		
		$view = $this->view($photos, 201);
		return $this->handleView($view);
	}
}