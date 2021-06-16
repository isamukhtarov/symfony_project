<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Widget;

use Ria\Bundle\PostBundle\Entity\Widget\Type;
use Ria\Bundle\CoreBundle\Helper\StringHelper;

class CreateWidgetCommand
{
    public int $id;

    public function __construct(
        private string $content
    ){}

    public function getContent(): string
    {
        return StringHelper::clearEmoji($this->content);
    }

    public function getType(): Type
    {
        return Type::createFromContent($this->content);
    }
}