<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\Region;

use Ria\Bundle\PostBundle\Command\Region\RegionTranslationCommand;
use Symfony\Component\Form\AbstractType;
use Ria\Bundle\AdminBundle\Form\Type\MetaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Ria\Bundle\PostBundle\Command\Story\StoryTranslationCommand;

class RegionTranslationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'form.title'
            ])->add('slug', TextType::class, [
                'label' => 'form.slug'
            ])
            ->add('locale', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RegionTranslationCommand::class,
        ]);
    }

}