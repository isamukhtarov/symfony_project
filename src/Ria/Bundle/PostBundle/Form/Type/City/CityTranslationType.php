<?php


namespace Ria\Bundle\PostBundle\Form\Type\City;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Ria\Bundle\PostBundle\Command\City\CityTranslationCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
               ->add('title', TextType::class, [
                   'label' => 'City'
               ])
               ->add('slug', TextType::class, [
                   'label' => 'form.slug'
               ])
               ->add('locale', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CityTranslationCommand::class,
        ]);
    }
}