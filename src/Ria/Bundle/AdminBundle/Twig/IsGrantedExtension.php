<?php
declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\Twig;

use Ria\Bundle\AdminBundle\Voter\AccessVoter;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IsGrantedExtension extends AbstractExtension
{
    public function __construct(
        private Security $security,
        private AccessVoter $voter,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_granted_to', [$this, 'isGrantedTo'], ['is_safe' => ['all']])
        ];
    }

    public function isGrantedTo(string $accessIdentifier): bool
    {
        return $this->voter->can($this->security->getUser(), $accessIdentifier);
    }

}