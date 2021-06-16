<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Form\Type\Person;

use Ria\Bundle\PhotoBundle\Form\Type\PhotoManagerMultipleType;
use Ria\Bundle\PhotoBundle\Form\Type\PhotoManagerSingleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PersonType
 * @package Ria\Bundle\PersonBundle\Form\Type\Person
 */
class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', CheckboxType::class, [
                'label' => 'form.status',
                'required' => false
            ])
            ->add('photos', PhotoManagerMultipleType::class)
            ->add('photo', PhotoManagerSingleType::class)
            ->add('translations', CollectionType::class, [
                'entry_type' =>  PersonTranslationType::class
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