<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\ExpertQuote;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ExpertQuoteType extends AbstractType
{
    public function __construct(
        private PostRepository $postRepository,
        private RouterInterface $router,
        private RequestStack $requestStack
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('language', HiddenType::class, [
                'data' => $this->getLanguage()
            ])
            ->add('postId', HiddenType::class, [
                'data' => $this->requestStack->getCurrentRequest()->attributes->get('id')
            ])
            ->add('expertId', ChoiceType::class, [
                'required' => true,
                'placeholder' => false,
                'label' => 'quote.Expert',
                'attr' => [
                    'data-selectable' => true,
                    'data-placeholder' => 'Select expert',
                    'data-language' => 'az', // Get current locale.
                    'data-type' => 'expert',
                    'data-multiple' => false,
                    'data-xhr-route' => $this->router->generate('persons.list'),
                ],
            ])
            ->add('text', CKEditorType::class, [
                    'config_name' => 'quote',
                    'required' => true,
                    'label' => 'quote.text'
            ]);
    }

    public function getLanguage(): string
    {
        $id = $this->requestStack->getCurrentRequest()->attributes->get('id');
        if ($id) {
            $post = $this->postRepository->find((int)$id);
            return $post->getLanguage();
        }

        return $this->requestStack->getCurrentRequest()->get('lang');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['csrf_protection' => true]);
    }
}