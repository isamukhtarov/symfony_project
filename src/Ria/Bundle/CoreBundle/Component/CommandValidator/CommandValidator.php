<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Component\CommandValidator;

use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\{ConstraintViolation, ConstraintViolationListInterface};

class CommandValidator implements CommandValidatorInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private array $errors = [],
    ){}

    public function validate(Validatable $command, Request|array $source): bool
    {
        $this->setValues($command, $source);
        $this->setErrors($this->validator->validate($command));
        return empty($this->getErrors());
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function setValues(Validatable $command, Request|array $source): void
    {
        $reflection = new ReflectionClass($command);
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $value = is_array($source) ? ($source[$property->getName()] ?? null) : $source->get($property->getName());
            $property->setValue($command, $value);
        }
    }

    private function setErrors(ConstraintViolationListInterface $violationList): void
    {
        /** @var ConstraintViolation $error */
        foreach ($violationList as $error)
            $this->errors[$error->getPropertyPath()] = $error->getMessage();
    }
}