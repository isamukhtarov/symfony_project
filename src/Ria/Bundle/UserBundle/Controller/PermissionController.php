<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Controller;

use League\Tactician\CommandBus;
use Ria\Bundle\UserBundle\Command\Permission\UpdatePermissionCommand;
use Ria\Bundle\UserBundle\Entity\Permission;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Ria\Bundle\UserBundle\Form\Grid\PermissionGrid;
use Ria\Bundle\UserBundle\Repository\PermissionRepository;
use Ria\Bundle\UserBundle\Form\Type\Permission\PermissionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Ria\Bundle\UserBundle\Command\Permission\CreatePermissionCommand;
use Ria\Bundle\UserBundle\Command\Permission\DeletePermissionCommand;

#[Route('/users/permissions', name: 'users.permissions.')]
class PermissionController extends AbstractController
{
    public function __construct(
        private CommandBus $bus,
        private PermissionRepository $permissionRepository,
    ){}

    #[Route('/', name: 'index')]
    public function index(PermissionGrid $grid): Response
    {
        return $this->render('@RiaUser/permissions/index.html.twig', [
            'grid' => $grid->createView(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $command = new CreatePermissionCommand();
        $form = $this->createForm(PermissionType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            $this->addFlash('success', 'Permission created successfully !');
            return $this->redirectToRoute('users.permissions.index');
        }

        return $this->render('@RiaUser/permissions/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(Permission $permission, Request $request): Response
    {
        $command = new UpdatePermissionCommand($permission);
        $form = $this->createForm(PermissionType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            $this->addFlash('success', 'Permission updated successfully !');
            return $this->redirectToRoute('users.permissions.update', ['id' => $permission->getId()]);
        }

        return $this->render('@RiaUser/permissions/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete($id): Response
    {
        $this->bus->handle(new DeletePermissionCommand((int) $id));
        $this->addFlash('success', 'Permission deleted successfully !');
        return $this->redirectToRoute('users.permissions.index');
    }
}