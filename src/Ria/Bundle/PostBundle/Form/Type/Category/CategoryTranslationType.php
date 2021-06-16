<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\Category;

use Ria\Bundle\AdminBundle\Form\Type\MetaType;
use Ria\Bundle\PostBundle\Command\Category\CategoryTranslationCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryTranslationType extends AbstractType
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
            ->add('meta', MetaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CategoryTranslationCommand::class,
        ]);
    }

}