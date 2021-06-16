<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Ria\Bundle\CoreBundle\Validation\Constraint\EntityExists;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EntityExistsValidator extends ConstraintValidator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ){}

    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof EntityExists))
            throw new UnexpectedTypeException($constraint, EntityExists::class);

        if (empty($value)) return;

        $data = $this->entityManager->getRepository($constraint->entity)->findOneBy([
            $constraint->property => $value,
        ]);

        if ($data === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%entity%', $constraint->entity)
                ->setParameter('%property%', $constraint->property)
                ->setParameter('%value%', $value)
                ->addViolation();
        }
    }
}