<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Form\Type;

use Ria\Bundle\PhotoBundle\Form\Type\PhotoManagerSingleType;
use Ria\Bundle\VoteBundle\Form\DataTransformer\TypeDataTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, HiddenType, TextType, CheckboxType, SubmitType};
use Symfony\Component\Form\FormBuilderInterface;

class VoteType extends AbstractType
{
    public function __construct(
        private TypeDataTransformer $typeDataTransformer
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'required' => true,
            'label' => 'form.title'
        ])
            ->add('status', CheckboxType::class, [
                'required' => false,
                'label' => 'form.status'
            ])
            ->add('showRecaptcha', CheckboxType::class, [
                'required' => false,
                'label' => 'form.showRecaptcha'
            ])
            ->add('showOnMainPage', CheckboxType::class, [
                'required' => false,
                'label' => 'form.showOnMainPage'
            ])
            ->add('locale', HiddenType::class)
            ->add('startDate', TextType::class, [
                'required' => true,
                'label' => 'form.startDate',
                'attr' => ['date-plugin' => 'datepicker'],
                'data' => date('Y-m-d'),
            ])
            ->add('type', ChoiceType::class, [
                'choices' => $this->typeDataTransformer->transform(),
                'required' => true,
                'label' => 'form.type'
            ])
            ->add('photo', PhotoManagerSingleType::class)
            ->add('endDate', TextType::class, [
                'required' => false,
                'label' => 'form.endDate',
                'attr' => ['date-plugin' => 'datepicker'],
            ])
            ->add('options', CollectionType::class, [
                'entry_type' => VoteOptionType::class,
                'allow_add'  => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => true
        ]);
    }
}