<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\Post;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ChangePostDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cause', TextareaType::class, [
                'required' => true,
                'attr' => ['rows' => 6],
            ])
            ->add('date', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => ['data-plugin' => 'datetimepicker'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
                'attr' => ['class' => 'btn btn-success float-right btn-round'],
            ]);
    }
}