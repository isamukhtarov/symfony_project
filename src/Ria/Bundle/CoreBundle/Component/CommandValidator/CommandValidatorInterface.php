<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Component\CommandValidator;

use Symfony\Component\HttpFoundation\Request;

interface CommandValidatorInterface
{
    public function validate(Validatable $command, Request|array $source): bool;
    public function getErrors(): array;
}