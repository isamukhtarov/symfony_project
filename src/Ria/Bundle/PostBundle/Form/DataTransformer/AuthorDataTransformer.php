<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\DataTransformer;

use Ria\Bundle\UserBundle\Enum\UserPermissions;
use Ria\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AuthorDataTransformer
{
    public function __construct(
        private UserRepository $userRepository,
        private ParameterBagInterface $parameterBag,
    ){}

    public function transform(array $params = []): array
    {
        $data = [];
        $language = $params['language'] ?? $this->parameterBag->get('app.locale');
        foreach ($this->userRepository->getByPermission(UserPermissions::CAN_BE_AUTHOR) as $user)
            $data[$user->getTranslation($language)->getFullName()] = $user->getId();
        return $data;
    }
}