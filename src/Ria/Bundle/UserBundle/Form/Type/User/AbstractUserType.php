<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\{FormEvent, FormEvents};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ria\Bundle\UserBundle\Form\Modifier\UserFormModifier;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Ria\Bundle\PhotoBundle\Form\Type\PhotoManagerSingleType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

abstract class AbstractUserType extends AbstractType
{
    public function __construct(
        private UserFormModifier $userFormModifier,
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'form.email',
            ])
            ->add('emailAdditional', EmailType::class, [
                'required' => false,
                'label' => 'form.emailAdditional',
            ])
            ->add('gender', ChoiceType::class, [
                'required' => true,
                'label' => 'form.gender',
                'choices' => [
                    'male'   => 'male',
                    'female' => 'female',
                ],
            ])
            ->add('phone', IntegerType::class, [
                'required' => false,
                'label' => 'form.phone',
            ])
            ->add('birthDate', BirthdayType::class, [
                'required' => true,
                'label' => 'form.birthDate',
                'placeholder' => ['year' => 'form.year', 'month' => 'form.month', 'day' => 'form.day']
            ])
            ->add('status', ChoiceType::class, [
                'required' => true,
                'label' => 'form.status',
                'choices' => [
                    'Inactive' => 0, 'Active' => 1
                ],
            ])
            ->add('photo', PhotoManagerSingleType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
                'attr' => ['class' => 'btn btn-lg btn-primary btn-block'],
            ]);

        $builder->add('translations', CollectionType::class, [
            'entry_type' => UserTranslationType::class,
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $this->userFormModifier->modifyFields($event->getForm(), $event->getData());
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['csrf_protection' => true]);
    }
}