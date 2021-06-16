<?php

declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\Controller\Auth;

use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route("/auth", name: "auth.")]
class LoginController extends AbstractController
{
    #[Route("/login", name: "login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser())
            return $this->redirectToRoute('admin.index');

        return $this->render('@RiaAdmin/security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'lastUsername' => $authenticationUtils->getLastUsername(),
        ]);
    }

    #[Route("/logout", name: "logout")]
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}