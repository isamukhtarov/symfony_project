<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Constraint;

use Attribute;
use Ria\Bundle\CoreBundle\Validation\Validator\UniqueEntityValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute] class CommandUniqueEntity extends Constraint
{
    public const NOT_UNIQUE_ERROR = 'e777db8d-3af0-41f6-8a73-55255375cdca';

    protected static $errorNames = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    public string $entityClass;
    public string|null $errorPath = null;
    public array $fieldMapping = [];
    public bool $ignoreNull = true;
    public string $message = 'This value is already used.';
    public string $repositoryMethod = 'findBy';

    public function getDefaultOption(): string
    {
        return 'entityClass';
    }

    public function getRequiredOptions(): array
    {
        return ['fieldMapping', 'entityClass'];
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return UniqueEntityValidator::class;
    }
}