<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationUserType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class IndexController extends AbstractController
{
    use TargetPathTrait;
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, Security $security, AuthenticationUtils $helper)
    {
        if ($security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_index');
        }

        $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('admin_index'));

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function registration(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(RegistrationUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $users = new Users();
            $users->setLogin($form->get('login')->getData());
            $users->setPassword($encoder->encodePassword($users, $form->get('password')->getData()));
            $users->setRole($form->get('role')->getData());

            $em->persist($users);
            $em->flush();

            return $this->redirectToRoute('index');
        }


        return $this->render('index/registartion.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView(),
        ]);
    }
}
