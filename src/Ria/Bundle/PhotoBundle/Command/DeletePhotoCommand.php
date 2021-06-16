<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Command;

class DeletePhotoCommand
{
    public function __construct(
        public int $id
    ) {}
}