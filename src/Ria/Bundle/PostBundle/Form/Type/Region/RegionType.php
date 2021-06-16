<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\Region;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RegionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort', NumberType::class, [
                'label'    => 'form.sort',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
            ]);

        $builder->add('translations', CollectionType::class, [
            'entry_type' => RegionTranslationType::class,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['csrf_protection' => true]);
    }
}