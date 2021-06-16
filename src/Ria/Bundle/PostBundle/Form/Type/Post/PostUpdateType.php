<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\Post;

use Symfony\Component\Form\{FormEvent, FormEvents, FormBuilderInterface};

class PostUpdateType extends AbstractPostType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // Setting values to form.
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $this->postFormModifier->modifyFields($event->getForm(), $this->data->toArray());
            })
            ->addEventSubscriber($this->postIsBusyListener);
    }
}