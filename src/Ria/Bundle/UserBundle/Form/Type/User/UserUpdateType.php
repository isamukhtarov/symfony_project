<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Form\Type\User;

use Ria\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Ria\Bundle\CoreBundle\Validation\Constraint\UniqueColumn;

class UserUpdateType extends AbstractUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'validateEmail']);
    }

    public function validateEmail(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($form->getData()->email === $data['email'])
            return;

        $form->add('email', EmailType::class, [
            'required' => true,
            'label' => 'form.email',
            'constraints' => [new UniqueColumn(['entity' => User::class, 'column' => 'email'])],
        ]);
    }
}
