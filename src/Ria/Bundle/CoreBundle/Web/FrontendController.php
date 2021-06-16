<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Web;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FrontendController
 * @package Ria\Bundle\CoreBundle\Web
 */
class FrontendController extends AbstractController
{

    public function __construct(protected EntityManagerInterface $em)
    {
    }

    public function render(string $view, array $parameters = [], Response $response = null): Response
    {
        if (!str_contains($view, '@RiaWeb/')) {
            $theme = 'default';
            $view  = "@RiaWeb/{$theme}/index/" . $view;
        }

        return parent::render($view, $parameters, $response);
    }

}