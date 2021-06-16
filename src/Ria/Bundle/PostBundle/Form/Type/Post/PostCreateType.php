<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\Post;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\{FormBuilderInterface, FormEvent, FormEvents};

class PostCreateType extends AbstractPostType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if ($this->data->isCreationOfTranslation())
            $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addTranslatorField'])
                ->addEventSubscriber($this->postIsBusyListener);
    }

    public function addTranslatorField(FormEvent $event)
    {
        $event->getForm()->add('translatorId', ChoiceType::class, [
            'choices' => $this->translatorDataTransformer->transform(['language' => $this->data->language]),
            'required' => true,
            'placeholder' => '-',
            'label' => 'form.translator',
            'attr' => ['data-selectable' => true],
        ]);
    }
}