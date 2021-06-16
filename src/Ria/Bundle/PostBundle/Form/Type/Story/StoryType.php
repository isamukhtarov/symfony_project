<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\Story;

use Ria\Bundle\PhotoBundle\Form\Type\PhotoManagerSingleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoryType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', CheckboxType::class, [
                'label'    => 'form.status',
                'required' => false,
            ])
            ->add('showOnSite', CheckboxType::class, [
                'label'    => 'show_on_site',
                'required' => false,
            ])
            ->add('cover', PhotoManagerSingleType::class)
            ->add('translations', CollectionType::class, [
                'entry_type' => StoryTranslationType::class,
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