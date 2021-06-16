<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Form\Type\Permission;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Ria\Bundle\UserBundle\Entity\Permission;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Ria\Bundle\CoreBundle\Validation\Constraint\UniqueColumn;
use Ria\Bundle\UserBundle\Command\Permission\UpdatePermissionCommand;

class PermissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Name',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
                'attr' => ['class' => 'btn btn-lg btn-primary btn-block'],
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'checkForPermissionName']);
    }

    public function checkForPermissionName(FormEvent $event): void
    {
        $form = $event->getForm();
        $formData = $form->getData();
        $data = $event->getData();

        if (!($formData instanceof UpdatePermissionCommand) || ($formData->name === $data['name']))
            return;

        $form->add('name', TextType::class, [
            'required'    => true,
            'label'       => 'form.name',
            'constraints' => [new UniqueColumn(['entity' => Permission::class, 'column' => 'name'])],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['csrf_protection' => true]);
    }
}
