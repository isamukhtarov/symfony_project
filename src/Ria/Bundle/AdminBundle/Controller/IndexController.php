<?php

declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response, Cookie};

/**
 * Class IndexController
 * @package Ria\Bundle\AdminBundle\Controller
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="admin.index")
     */
    public function index(): Response
    {
        return $this->render('@RiaAdmin/index.html.twig');
    }

    #[Route('/change-language/', name: "change_language", methods: ["POST", "GET"])]
    public function changeLanguage(Request $request): Response
    {
        $language           = $request->get('language');
        $supportedLanguages = $this->getParameter('admin.languages');

        if (!in_array($language, $supportedLanguages))
            throw $this->createNotFoundException();

        $cookie = new Cookie(
            name: 'language',
            value: $language,
            expire: new \DateTime('+1 month'),
            path: '/',
            domain: null,
            secure: false,
            httpOnly: false,
        );

        $response = new Response();
        $response->headers->setCookie($cookie);
        $response->sendHeaders();

        return $response;
    }
}