<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Grid\Column;

use Ria\Bundle\PostBundle\Entity\Post\Status;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostStatusColumn
{
    public function __construct(
        private TranslatorInterface $translator
    ){}

    public function render(string $status): string
    {
        $badgeClass = match ($status) {
            Status::READ => 'success',
            Status::CREATED, Status::ON_MODERATION, Status::WAITING_FOR_CORRECTION => 'info',
            Status::PRIVATE => 'warning',
            Status::DELETED, Status::ARCHIVED => 'danger',
            default => 'default',
        };

        return sprintf('<span class="badge badge-outline badge-%s">%s</span>', $badgeClass, $this->translator->trans($status));
    }
}