<?php

declare(strict_types=1);

namespace Ria\Bundle\YoutubeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class YoutubeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('youtubeId', TextType::class, [
            'label' => 'Youtube Id',
            'required' => true
        ])
            ->add('status', CheckboxType::class, [
                'label' => 'form.status',
                'required' => false
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