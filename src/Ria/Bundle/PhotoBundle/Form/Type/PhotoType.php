<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', CollectionType::class, [
                'entry_type' => PhotoTranslationType::class,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
        ]);
    }

}