<?php

declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Form\Type\Person;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use JetBrains\PhpStorm\ArrayShape;
use Ria\Bundle\AdminBundle\Form\Type\MetaType;
use Ria\Bundle\PersonBundle\Command\Person\PersonTranslationCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PersonTranslationType
 * @package Ria\Bundle\PersonBundle\Form\Type\Person
 */
class PersonTranslationType extends AbstractType
{
    public function __construct(
        private RequestStack $requestStack
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, [
                'label' => 'form.first_name',
                'attr' => ['data-id' => 'firstName']
            ])
            ->add('last_name', TextType::class, [
                'label' => 'form.last_name',
                'attr' => ['data-id' => 'lastName']
            ])
            ->add('position', TextType::class, [
                'label' => 'form.position'
            ])
            ->add('slug', TextType::class, [
                'label' => 'form.slug'
            ])
            ->add('text', CKEditorType::class, $this->getEditorConfigs())

            ->add('locale', HiddenType::class)
            ->add('meta', MetaType::class);
    }

    private function getEditorConfigs(): array
    {
        if (!in_array($this->requestStack->getCurrentRequest()->get('_route'), ['experts.create', 'experts.update'])) {
            return $this->personConfig();
        }

        return $this->expertConfig();
    }

    #[ArrayShape([
        'config' => 'array',
        'label' => 'string'
    ])]
    private function expertConfig():  array
    {
        return [
            'config' => [
                'config_name' => 'admin',
                'title' => '',
                'required' => false
            ],
            'label' => 'form.text'
        ];
    }

    #[ArrayShape([
        'config' => 'array',
        'plugins' => 'array',
        'label' => 'string'
    ])]
    private function personConfig(): array
    {
        return [
            'config' => [
                'config_name' => 'admin',
                'title' => '',
                'required' => false,
                'contentsCss' => '/admin/css/ckeditor-contents.css',
                'extraPlugins' => 'addTimeline'
            ],
            'plugins' => [
                'addTimeline' => [
                    'path' => '/admin/ckeditor/plugins/addPersonTimeLine/',
                    'filename' => 'plugin.js'
                ],
            ],
            'label' => 'form.text',
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PersonTranslationCommand::class
        ]);
    }
}