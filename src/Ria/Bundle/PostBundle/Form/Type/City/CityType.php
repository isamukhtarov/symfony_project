<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\City;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Ria\Bundle\PostBundle\Query\Repository\RegionRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Ria\Bundle\PostBundle\Form\DataTransformer\RegionDataTransformer;

class CityType extends AbstractType
{
    public function __construct(
        private RegionDataTransformer $regionDataTransformer,
        private RegionRepository $regionRepository
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             ->add('regionId', ChoiceType::class, [
                 'placeholder' => false,
                 'label' => 'Region',
                 'choices' => $this->regionDataTransformer->transform(['language' => $builder->getData()->getCurrentLocale()]),
             ])
             ->add('submit', SubmitType::class, [
                  'label' => 'form.submit'
             ]);

        $builder->add('translations', CollectionType::class, [
            'entry_type' => CityTranslationType::class,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['csrf_protection' => true]);
    }
}
