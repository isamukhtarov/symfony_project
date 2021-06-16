<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\Story;

use Ria\Bundle\AdminBundle\Form\Type\MetaType;
use Ria\Bundle\PostBundle\Command\Story\StoryTranslationCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoryTranslationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'form.title'
            ])->add('slug', TextType::class, [
                'label' => 'form.slug'
            ])->add('description', TextareaType::class, [
                'label'    => 'form.description',
                'required' => false,
            ])
            ->add('locale', HiddenType::class)
            ->add('meta', MetaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StoryTranslationCommand::class,
        ]);
    }

}