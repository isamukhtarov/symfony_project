<?php

declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Select2Type extends AbstractType
{
    public function __construct(
        private RouterInterface $router
    ){}

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['allow_add'] = $options['allow_add'];
        $view->vars['select_placeholder'] = $options['select_placeholder'];

        $view->vars['xhr_route'] = isset($options['ajax']['route'])
            ? $this->router->generate($options['ajax']['route'])
            : null;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'select_placeholder' => null,
            'label'       => null,
            'field'       => null,
            'required'    => false,
            'placeholder' => false,
            'multiple'    => false,
            'allow_add'   => false,
            'xhr_route'   => null,
            'compound'    => true,
            'ajax'        => [],
        ]);

        $resolver->setAllowedTypes('multiple', 'bool');
        $resolver->setAllowedTypes('allow_add', 'bool');
        $resolver->setAllowedTypes('ajax', 'array');
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'ria_select2';
    }
}