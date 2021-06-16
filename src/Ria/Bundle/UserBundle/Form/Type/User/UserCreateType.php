<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Form\Type\User;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{PasswordType, RepeatedType};

class UserCreateType extends AbstractUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        // Adding password field just on create form.
        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'first_options' => ['label' => 'form.password'],
            'second_options' => ['label' => 'form.passwordConfirmation'],
        ]);
    }
}
