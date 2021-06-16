<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Form\Type;

use Ria\Bundle\PhotoBundle\Command\PhotoTranslationCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotoTranslationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author', TextType::class, [
                'label' => 'photo.author',
                'required' => false,
            ])->add('information', TextType::class, [
                'label' => 'photo.information',
                'required' => false,
            ])->add('source', TextType::class, [
                'label'    => 'photo.source',
                'required' => false,
            ])
            ->add('locale', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PhotoTranslationCommand::class,
        ]);
    }
}