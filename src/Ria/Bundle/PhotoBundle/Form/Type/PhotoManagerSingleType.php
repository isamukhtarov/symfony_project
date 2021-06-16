<?php

declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Form\Type;

use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PhotoBundle\Command\PhotoCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ria\Bundle\PhotoBundle\Query\Repository\PhotoRepository;
use Symfony\Component\Form\{AbstractType, FormInterface, FormView};

class PhotoManagerSingleType extends AbstractType
{
    public function __construct(
        private PhotoRepository $photoRepository,
    ){}

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'error_bubbling' => true,
            'compound' => false,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var PhotoCommand $value */
        $value = &$view->vars['value'];

        if (!$value instanceof Photo) {
            $value = $this->transform($value);
        }

        parent::buildView($view, $form, $options);
    }

    public function transform($value): ?Photo
    {
        return $this->photoRepository->find($value);
    }
}