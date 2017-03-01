<?php
namespace UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use UsersBundle\Entity\User;
use UsersBundle\Form\UserType;

class MainController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function signUpAction(Request $request)
    {
        $user = new User();
        $form = $this->get('form.factory')->create(UserType::class, $user);

        if($request->isMethod('post') && $form->handleRequest($request))
        {
            $encoded = $this->container->get('security.password_encoder')->encodePassword($user, $user->getPassword());

            $user->setPassword($encoded);

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashbag()->add('info', 'Votre inscription a été validée !');

            return $this->redirectToRoute('users_signin');
        }

        return $this->render('UsersBundle::signup.html.twig', array('form' => $form->createView()));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function signInAction()
    {
        return $this->render('UsersBundle::signin.html.twig', array());
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function viewProfileAction()
    {
        $this->denyAccessUnlessGranted('ROLE_MEMBER', null, 'Veuillez vous connecter pour accéder à la page.');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('UsersBundle::viewProfile.html.twig', array('user' => $user));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function viewAdvertsAction()
    {
        $this->denyAccessUnlessGranted('ROLE_MEMBER', null, 'Veuillez vous connecter pour accéder à la page.');

        $annonces = $this->getDoctrine()->getManager()->getRepository('AnnoncesBundle:Annonce')->findBy(array('owner' => $this->getUser()));

        return $this->render('UsersBundle::viewAdverts.html.twig', array('annonces' => $annonces));
    }

}
