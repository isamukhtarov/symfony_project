<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Twig;

use Psr\Container\ContainerInterface;
use Ria\Bundle\PostBundle\Form\Type\ExpertQuote\ExpertQuoteType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ExpertQuoteFormExtension extends AbstractExtension
{
    public function __construct(
        private ContainerInterface $container,
    ){}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('quoteForm', [$this, 'getForm'], ['is_safe' => ['all']]),
        ];
    }

    public function getForm(): FormView
    {
        $form = $this->createFormBuilder(ExpertQuoteType::class);
        return $form->createView();
    }

    public function createFormBuilder(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }
}