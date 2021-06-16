<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Controller;

use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ria\Bundle\PostBundle\Form\Grid\CategoryGrid;
use Ria\Bundle\PostBundle\Enum\CategoryPermissions;
use Ria\Bundle\PostBundle\Entity\Category\Category;
use Ria\Bundle\PostBundle\Form\Type\Category\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Ria\Bundle\PostBundle\Command\Category\{CreateCategoryCommand, UpdateCategoryCommand, DeleteCategoryCommand};

#[Route('posts/categories', name: 'categories.')]
class CategoryController extends AbstractController
{
    public function __construct(
        private CommandBus $bus,
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CategoryGrid $grid): Response
    {
        $this->denyAccessUnlessGranted(CategoryPermissions::MANAGE);

        return $this->render('@RiaPost/categories/index.html.twig', [
            'grid' => $grid->createView(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $command = new CreateCategoryCommand($this->getParameter('app.supported_locales'), $request->getLocale());

        $form = $this->createForm(CategoryType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('categories.index');
        }

        return $this->render('@RiaPost/categories/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/update', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function update(Category $category, Request $request): Response
    {
        $command = new UpdateCategoryCommand($category, $this->getParameter('app.supported_locales'), $request->getLocale());

        $form = $this->createForm(CategoryType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('categories.index');
        }

        return $this->render('@RiaPost/categories/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET', 'POST'])]
    public function delete($id): Response
    {
        $this->bus->handle(new DeleteCategoryCommand((int) $id));
        return $this->redirectToRoute('categories.index');
    }
}