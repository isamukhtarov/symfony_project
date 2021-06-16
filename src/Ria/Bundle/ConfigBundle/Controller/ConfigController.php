<?php

declare(strict_types=1);

namespace Ria\Bundle\ConfigBundle\Controller;

use League\Tactician\CommandBus;
use Ria\Bundle\ConfigBundle\Command\ConfigUpdateCommand;
use Ria\Bundle\ConfigBundle\Entity\Config;
use Ria\Bundle\ConfigBundle\Form\Grid\ConfigGrid;
use Ria\Bundle\ConfigBundle\Form\Type\ConfigType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('configs', name: 'configs.')]
class ConfigController extends AbstractController
{
    public function __construct(
        private CommandBus $bus
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ConfigGrid $grid): Response
    {
        return $this->render('@RiaConfig/index.html.twig', [
            'grid' => $grid->createView()
        ]);
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(Config $config, Request $request): Response
    {
        $command = new ConfigUpdateCommand($config);

        $form = $this->createForm(ConfigType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('configs.index');
        }

        return $this->render('@RiaConfig/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}