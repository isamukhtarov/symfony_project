<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Ria\Bundle\CoreBundle\Validation\Constraint\UniqueColumn;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueColumnValidator extends ConstraintValidator
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ){}

    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof UniqueColumn))
            throw new UnexpectedTypeException($constraint, UniqueColumn::class);

        $repository = $this->entityManager->getRepository($constraint->entity);

        if ($repository->findOneBy([$constraint->column => $value]) !== null)
            $this->context->buildViolation($constraint->message)->setParameter('{{ field }}', $value)->addViolation();
    }
}