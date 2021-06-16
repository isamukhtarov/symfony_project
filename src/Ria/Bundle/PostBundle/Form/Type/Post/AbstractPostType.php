<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\Post;

use Symfony\Component\Form\AbstractType;
use Ria\Bundle\PostBundle\Enum\OptionType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Ria\Bundle\AdminBundle\Form\Type\MetaType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ria\Bundle\PostBundle\Form\Modifier\PostFormModifier;
use Ria\Bundle\PostBundle\Command\Post\AbstractPostCommand;
use Ria\Bundle\PhotoBundle\Form\Type\PhotoManagerMultipleType;
use Ria\Bundle\PostBundle\Form\EventListener\PostIsBusyListener;
use Symfony\Component\Form\{FormInterface, FormBuilderInterface, FormEvent, FormEvents, FormView};
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType, TextType, ChoiceType, HiddenType, SubmitType, TextareaType,
};
use Ria\Bundle\PostBundle\Form\DataTransformer\{
    CityDataTransformer, IconDataTransformer, TranslatorDataTransformer, TypeDataTransformer,
    StoryDataTransformer, StatusDataTransformer, CategoryDataTransformer, AuthorDataTransformer,
    ExportDataTransformer, PersonDataTransformer,
};

abstract class AbstractPostType extends AbstractType
{
    public const SUBMIT_AND_STAY = 'submit';
    public const SUBMIT_AND_EXIT = 'submitAndExit';

    protected AbstractPostCommand $data;

    public function __construct(
        protected RouterInterface $router,
        protected PostFormModifier $postFormModifier,
        protected PostIsBusyListener $postIsBusyListener,
        protected CategoryDataTransformer $categoryDataTransformer,
        protected CityDataTransformer $cityDataTransformer,
        protected TypeDataTransformer $typeDataTransformer,
        protected StoryDataTransformer $storyDataTransformer,
        protected StatusDataTransformer $statusDataTransformer,
        protected AuthorDataTransformer $authorDataTransformer,
        protected PersonDataTransformer $personDataTransformer,
        protected TranslatorDataTransformer $translatorDataTransformer,
        protected IconDataTransformer $iconDataTransformer,
        protected ExportDataTransformer $exportDataTransformer,
        protected TranslatorInterface $translator,
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->data = $builder->getData();

        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'form.title',
            ])
            ->add('markedWords', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'placeholder' => false,
                'label' => 'form.markedWords',
                'attr' => [
                    'data-selectable' => true,
                    'data-placeholder' => $this->translator->trans('form.enterMarkedWords'),
                    'data-tags' => true,
                    'data-multiple' => true,
                ],
            ])
            ->add('slug', TextType::class, [
                'required' => true,
                'label' => $this->translator->trans('form.slug'),
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => $this->translator->trans('form.description'),
            ])
            ->add('tags', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'placeholder' => false,
                'label' => 'form.tags',
                'attr' => [
                    'data-selectable' => true,
                    'data-placeholder' => $this->translator->trans('form.enterTags'),
                    'data-language' => $this->data->language,
                    'data-tags' => true,
                    'data-multiple' => true,
                    'data-xhr-route' => $this->router->generate('tags.list'),
                ],
            ])
            ->add('persons', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'placeholder' => false,
                'label' => 'form.persons',
                'attr' => [
                    'data-selectable' => true,
                    'data-placeholder' => $this->translator->trans('form.enterPersons'),
                    'data-language' => $this->data->language,
                    'data-type' => 'person',
                    'data-multiple' => true,
                    'data-xhr-route' => $this->router->generate('persons.list'),
                ],
            ])
            ->add('photos', PhotoManagerMultipleType::class)
            ->add('content', CKEditorType::class, [
                'label' => 'form.content',
                'config' => [
                    'required' => false,
                    'title' => '',
                    'contentsCss' => '/bundles/riapost/css/contents.css',
                    'extraPlugins' => 'addMedia,addExpertQuote,addTimeline,addVote,wordcount'
                ],

                'plugins' => [
                    'addMedia' => [
                        'path' => '/admin/ckeditor/plugins/addMediaPost/',
                        'filename' => 'plugin.js'
                    ],

                    'addExpertQuote' => [
                        'path' => '/admin/ckeditor/plugins/addExpertQuote/',
                        'filename' => 'plugin.js'
                    ],

                    'addTimeline' => [
                        'path' => '/admin/ckeditor/plugins/addTimeline/',
                        'filename' => 'plugin.js'
                    ],

                    'addVote' => [
                        'path' => '/admin/ckeditor/plugins/addVote/',
                        'filename' => 'plugin.js'
                    ],

                    'wordcount' => [
                        'path' => '/admin/ckeditor/plugins/addCharactersCounter/',
                        'filename' => 'plugin.js'
                    ]
                ]
            ])
            ->add('icon', ChoiceType::class, [
                'choices' => $this->iconDataTransformer->transform(),
                'placeholder' => '-',
                'required' => false,
                'label' => 'form.icon',
            ])
            ->add('source', TextType::class, [
                'required' => false,
                'label' => 'form.source',
            ])
            ->add('note', TextType::class, [
                'required' => false,
                'label' => 'form.note',
            ])
            ->add('relatedPosts', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'placeholder' => false,
                'label' => 'form.relatedPosts',
                'attr' => [
                    'data-selectable' => true,
                    'data-placeholder' => $this->translator->trans('form.enterRelatedPost'),
                    'data-language' => $this->data->language,
                    'data-multiple' => true,
                    'data-xhr-route' => $this->router->generate('posts.list'),
                ],
            ])
            ->add('exports', ChoiceType::class, [
                'multiple'  => true,
                'required' => false,
                'label'    => false,
                'choices'  => $this->exportDataTransformer->transform(),
                'attr' => ['data-plugin' => 'multiSelect']
            ])
            // Settings
            ->add('categoryId', ChoiceType::class, [
                'placeholder' => $this->translator->trans('form.selectCategory'),
                'choices' => $this->categoryDataTransformer->transform(['language' => $this->data->language]),
                'required' => true,
                'label' => 'form.category',
            ])
            ->add('cityId', ChoiceType::class, [
                'choices' => $this->cityDataTransformer->transform(['language' => $this->data->language]),
                'placeholder' => '-',
                'required' => false,
                'label' => 'form.city',
            ])
            ->add('type', ChoiceType::class, [
                'choices' => $this->typeDataTransformer->transform(),
                'placeholder' => '-',
                'required' => true,
                'label' => 'form.type',
                'attr' => ['class' => 'type-select'],
            ])
            ->add('authorId', ChoiceType::class, [
                'choices' => $this->authorDataTransformer->transform(['language' => $this->data->language]),
                'placeholder' => '-',
                'required' => false,
                'label' => 'form.author',
                'attr' => ['data-selectable'  => true],
            ])
            ->add('expertId', ChoiceType::class, [
                'choices' => $this->personDataTransformer->transform(['language' => $this->data->language]),
                'placeholder' => '-',
                'label' => 'form.expert',
                'required' => false,
                'attr' => [
                    'class' => 'type-depended',
                    'data-selectable' => true,
                    'data-accept' => 'opinion',
                ],
            ])
            ->add('storyId', ChoiceType::class, [
                'choices' => $this->storyDataTransformer->transform(['language' => $this->data->language]),
                'placeholder' => '-',
                'required' => false,
                'label' => 'form.story',
            ])
            ->add('youtubeId', TextType::class, [
                'required' => false,
                'label' => 'form.youtube',
            ])
            ->add('optionType', ChoiceType::class, [
                'choices' => array_flip(OptionType::values()),
                'placeholder' => '-',
                'required' => false,
                'label' => 'form.option',
            ])
            ->add('publishedAt', TextType::class, [
                'required' => true,
                'label' => 'form.publishedAt',
                'attr' => ['data-plugin' => 'datetimepicker'],
            ])
            ->add('attachCurrentTime', CheckboxType::class, [
                'required' => false,
                'label' => 'form.attachCurrentTime',
                'attr' => ['checked' => true],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => $this->statusDataTransformer->transform(),
                'required' => true,
                'label' => 'form.status',
            ])
            // Flags
            ->add('isMain', CheckboxType::class, [
                'label' => 'form.isMain',
                'required' => false,
            ])
            ->add('isExclusive', CheckboxType::class, [
                'label' => 'form.isExclusive',
                'required' => false,
            ])
            ->add('isActual', CheckboxType::class, [
                'label' => 'form.isActual',
                'required' => false,
            ])
            ->add('isBreaking', CheckboxType::class, [
                'label' => 'form.isBreaking',
                'required' => false,
            ])
            ->add('isImportant', CheckboxType::class, [
                'label' => 'form.isImportant',
                'required' => false,
            ])
            ->add('linksNoIndex', CheckboxType::class, [
                'label' => 'form.linksNoIndex',
                'attr' => ['checked' => true],
                'required' => false,
            ])
            ->add('language', HiddenType::class)
            ->add('meta', MetaType::class)

            // Buttons
            ->add(self::SUBMIT_AND_STAY, SubmitType::class, [
                'label' => 'form.submit',
                'attr' => ['class' => 'btn  btn-success'],
            ])
            ->add(self::SUBMIT_AND_EXIT, SubmitType::class, [
                'label' => 'form.submitAndExit',
                'attr' => ['class' => 'btn  btn-success'],
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $this->postFormModifier->modifyFields($event->getForm(), $event->getData());
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['isCreationOfTranslation'] = $this->data->isCreationOfTranslation();
        $view->vars['postId'] = $this->data->id;
        $view->vars['postWasPublished'] = $this->data->wasPublished;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['csrf_protection' => true]);
    }
}