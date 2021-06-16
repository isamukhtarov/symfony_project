<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangeUserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'form.password'],
                'second_options' => ['label' => 'form.passwordConfirmation'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
                'attr' => ['class' => 'btn btn-lg btn-primary btn-block'],
            ]);
    }
}