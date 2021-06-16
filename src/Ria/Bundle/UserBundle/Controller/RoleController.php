<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Controller;

use League\Tactician\CommandBus;
use Ria\Bundle\UserBundle\Entity\Role;
use Ria\Bundle\UserBundle\Form\Grid\RoleGrid;
use Symfony\Component\Routing\Annotation\Route;
use Ria\Bundle\UserBundle\Form\Type\Role\RoleType;
use Ria\Bundle\UserBundle\Repository\RoleRepository;
use Symfony\Component\HttpFoundation\{Request, Response};
use Ria\Bundle\UserBundle\Repository\PermissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Ria\Bundle\UserBundle\Command\Role\{CreateRoleCommand, DeleteRoleCommand, UpdateRoleCommand};

#[Route('/users/roles', name: 'users.roles.')]
class RoleController extends AbstractController
{
    public function __construct(
        private CommandBus $bus,
        private PermissionRepository $permissionRepository,
        private RoleRepository $roleRepository,
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(RoleGrid $grid): Response
    {
        return $this->render('@RiaUser/roles/index.html.twig', [
            'grid' => $grid->createView(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $command = new CreateRoleCommand($this->permissionRepository->findAll());
        $form = $this->createForm(RoleType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            $this->addFlash('success', 'Role created successfully !');
            return $this->redirectToRoute('users.roles.index');
        }

        return $this->render('@RiaUser/roles/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(Role $role, Request $request): Response
    {
        $command = new UpdateRoleCommand($role, $this->permissionRepository->findAll());
        $form = $this->createForm(RoleType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            $this->addFlash('success', 'Role update successfully !');
            return $this->redirectToRoute('users.roles.update', ['id' => $role->getId()]);
        }

        return $this->render('@RiaUser/roles/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete($id): Response
    {
        $this->bus->handle(new DeleteRoleCommand((int) $id));
        $this->addFlash('success', 'Role deleted successfully !');
        return $this->redirectToRoute('users.roles.index');
    }
}