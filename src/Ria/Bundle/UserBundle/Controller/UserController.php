<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Controller;

use League\Tactician\CommandBus;
use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\UserBundle\Form\Grid\UserGrid;
use Symfony\Component\Routing\Annotation\Route;
use Ria\Bundle\UserBundle\Enum\UserPermissions;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Ria\Bundle\UserBundle\Repository\{UserRepository, RoleRepository, PermissionRepository};
use Ria\Bundle\UserBundle\Form\Type\User\{ChangeUserPasswordType, UserCreateType, UserUpdateType};
use Ria\Bundle\UserBundle\Command\User\{ChangeUserPasswordCommand, CreateUserCommand, DeleteUserCommand, UpdateUserCommand};

#[Route('/users', name: 'users.')]
class UserController extends AbstractController
{
    public function __construct(
        private CommandBus $bus,
        private UserRepository $userRepository,
        private RoleRepository $roleRepository,
        private PermissionRepository $permissionRepository,
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(UserGrid $grid): Response
    {
        $this->denyAccessUnlessGranted(UserPermissions::MANAGE);

        return $this->render('@RiaUser/users/index.html.twig', [
            'grid' => $grid->createView(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $command = new CreateUserCommand(
            $this->roleRepository->findAll(),
            $this->permissionRepository->findAll(),
            $this->getParameter('app.supported_locales'),
        );

        $form = $this->createForm(UserCreateType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            $this->addFlash('success', 'User created successfully !');
            return $this->redirectToRoute('users.index');
        }

        return $this->render('@RiaUser/users/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(User $user, Request $request): Response
    {
        $command = new UpdateUserCommand(
            $user,
            $this->roleRepository->findAll(),
            $this->permissionRepository->findAll(),
            $this->getParameter('app.supported_locales'),
        );

        $form = $this->createForm(UserUpdateType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            return $this->redirectToRoute('users.update', ['id' => $user->getId()]);
        }

        return $this->render('@RiaUser/users/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/change-password', name: 'change-password', methods: ['GET', 'POST'])]
    public function changePassword(User $user, Request $request): Response
    {
        $command = new ChangeUserPasswordCommand($user);
        $form = $this->createForm(ChangeUserPasswordType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            $this->addFlash('success', 'User password has been changed successfully !');
            return $this->redirectToRoute('users.index');
        }

        return $this->render('@RiaUser/users/change-password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete(User $user): Response
    {
        if (!$user->getPosts()->isEmpty())
            throw new AccessDeniedHttpException('You can not delete this user because of existence of related posts.');

        $this->bus->handle(new DeleteUserCommand($user));
        $this->addFlash('success', 'User deleted successfully !');
        return $this->redirectToRoute('users.index');
    }
}