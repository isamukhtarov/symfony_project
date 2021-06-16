<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Form\Type\User;

use Ria\Bundle\UserBundle\Command\User\UserTranslationCommand;
use Symfony\Component\Form\AbstractType;
use Ria\Bundle\AdminBundle\Form\Type\MetaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'form.first_name',
                'required' => true,
                'constraints' => [new NotBlank(), new Length(['max' => 100])],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'form.last_name',
                'required' => true,
                'constraints' => [new NotBlank(), new Length(['max' => 100])],
            ])
            ->add('position', TextareaType::class, [
                'label'    => 'form.position',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'form.description',
                'required' => false,
            ])
            ->add('locale', HiddenType::class)
            ->add('meta', MetaType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserTranslationCommand::class,
        ]);
    }

}