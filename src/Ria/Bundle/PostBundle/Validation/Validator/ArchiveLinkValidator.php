<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Validation\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ArchiveLinkValidator extends ConstraintValidator
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
    ){}

    public function validate($value, Constraint $constraint)
    {
        $pattern = '/^' . str_replace('/', '\/', $this->parameterBag->get('domain'))
            . '\/((ru|en)\/)?([\w\-]+\/)?([\w\-]+)\/([\w\d\-]+)\//i';
        
        if (!preg_match($pattern, $value)) {
            $this->context->buildViolation('Incorrect post url.')->addViolation();
        }
    }
}