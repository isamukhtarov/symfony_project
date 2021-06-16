<?php

declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessVoter extends RiaAbstractVoter
{
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return ($user instanceof UserInterface) && $this->can($user, $attribute);
    }
}