<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\Speech;

use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SpeechType extends AbstractType
{
    public function __construct(
        private RequestStack $requestStack
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('postId', HiddenType::class, [
                'data' => 1
            ])
            ->add('file', FileType::class, [
                'label' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'audio/mp3'
                        ]
                    ])
                ],
                'attr' => [
                    'accept' => '.mp3',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit'
            ]);
    }
}