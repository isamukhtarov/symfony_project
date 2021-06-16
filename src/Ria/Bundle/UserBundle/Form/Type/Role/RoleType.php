<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Form\Type\Role;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ria\Bundle\UserBundle\Command\Role\UpdateRoleCommand;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'assignPermissions']);

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Name',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
                'attr' => ['class' => 'btn btn-lg btn-primary float-right'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['csrf_protection' => true]);
    }

    public function assignPermissions(FormEvent $event)
    {
        $command = $event->getData();
        $options = [
            'label' => 'form.permissions',
            'multiple' => true,
            'expanded' => true,
            'choices' => $command->getPermissions(),
            'choice_value' => 'id',
            'choice_label' => 'name',
            'data' => [],
        ];

        if ($command instanceof UpdateRoleCommand) {
            $selectedPermissions = $command->getRole()->getPermissions();
            $options['choice_attr'] = fn ($choice) => ($selectedPermissions->contains($choice)) ? ['checked' => 'checked'] : [];
        }

        $event->getForm()->add('permissions', ChoiceType::class, $options);
    }
}