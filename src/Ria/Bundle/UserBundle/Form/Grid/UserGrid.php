<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Form\Grid;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Ria\Bundle\UserBundle\Entity\Role;
use Ria\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\{Request, RequestStack};
use Ria\Bundle\DataGridBundle\Grid\{Grid, GridConfig, GridManager};

class UserGrid
{
    private ?Request $request;

    public function __construct(
        private UserRepository $userRepository,
        private GridManager $gridManager,
        private EntityManagerInterface $entityManager,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    public function createView(): Grid
    {
        $queryBuilder = $this->userRepository
            ->createQueryBuilder('u')
            ->select('u', 'ut')
            ->join('u.translations', 'ut', Join::WITH, 'ut.language = :language')
            ->orderBy('u.id', 'DESC')
            ->setParameter('language', $this->request->getLocale());

        $gridConfig = (new GridConfig())
            ->setQueryBuilder($queryBuilder)
            ->setCountFieldName('u.id')
            ->addField('u.id', [
                'label' => 'Id',
                'sortable' => true,
                'filterable' => true,
            ])
            ->addField('u.email', [
                'label' => 'Email',
                'filterable' => true,
            ])
            ->addField('ut.firstName', [
                'label' => 'first_name',
                'filterable' => true,
            ])
            ->addField('ut.lastName', [
                'label' => 'last_name',
                'filterable' => true,
            ])
            ->addField('role', [
                'label' => 'role',
                'formatValueCallback' => fn ($value, $row) => $this->getRole($value, $row),
//                'filterable' => true,
//                'filterData' => $this->getRolesList()
            ])
            ->addField('u.status', [
                'label' => 'Status',
                'uniqueId' => 'status',
                'autoEscape' => false,
                'filterable' => true,
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request);
    }

    private function getRole($value, $row): string
    {
        $user = $this->userRepository->find($row['u.id']);
        $roles = [];
        foreach ($user->getRolesRelation() as $role) {
            $roles[] = $role->getName();
        }
        return implode(',', $roles);
    }

    private function getRolesList(): array
    {
        /** @var Role[] $roles */
        $roles = $this->entityManager
            ->getRepository(Role::class)
            ->findAll();

        $result = [];
        foreach ($roles as $role) {
            $result[$role->getId()] = $role->getName();
        }

        return $result;
    }
}