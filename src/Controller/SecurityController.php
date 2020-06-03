<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /** 
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User;

        $form = $this->createform(RegistrationType::class, $user);

        dump($request);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash =$encoder->encodePassword($user, $user->getPassword());
            // On recupere le mot de passe du formulaire(non haché pour le moment) pour le transmettre a la méthode encodePassword()
            // qui va se chargé d'encoder / crypter / hacher le mot de passe

            $user->setPassword($hash); // On envoie le mot de passe haché dans le setteur de l'objet $user afin qu'il soit inséré dans la BDD

            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('security/registration.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /** 
     * @Route("/connexion", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    /* 
    * @Route("deconnexion", name="security_logout")
    */
    public function logout()
    {
        // cette fonction ne retourne rien, il nous suffit d'avoir une route pour la deconnexion (voir security.yaml / firewalls)
    }

    
}
