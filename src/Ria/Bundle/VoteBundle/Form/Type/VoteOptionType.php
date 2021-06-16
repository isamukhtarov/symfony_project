<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Form\Type;

use Ria\Bundle\VoteBundle\Command\VoteOptionsCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoteOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class, [
                'data' => 0
            ])
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'form.title'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VoteOptionsCommand::class
        ]);
    }
}