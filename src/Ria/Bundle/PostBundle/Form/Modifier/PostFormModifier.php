<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Modifier;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Entity\Post\Export;
use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Symfony\Component\HttpFoundation\RequestStack;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Ria\Bundle\PersonBundle\Query\Repositories\PersonRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PostFormModifier
{
    private string $language;
    private const MODIFIED_FIELDS = [
        'markedWords', 'tags', 'persons', 'exports', 'relatedPosts',
    ];

    public function __construct(
        private RequestStack $requestStack,
        private ParameterBagInterface $parameterBag,
        private EntityManagerInterface $entityManager,
    )
    {
        $this->language = $this->requestStack->getMasterRequest()->getLocale()
            ?? $this->parameterBag->get('app.locale');
    }

    public function modifyFields(FormInterface $form, array $data): void
    {
        foreach (self::MODIFIED_FIELDS as $field) {
            $options = call_user_func([$this, $field], $form->get($field), $data[$field] ?? []);
            $form->add($field, ChoiceType::class, $options); // You can define form type dynamically for each field also.
        }
    }

    private function markedWords(FormInterface $form, array $data): array
    {
        return $this->defaultModification($form, $data);
    }

    private function tags(FormInterface $form, array $data): array
    {
        return $this->defaultModification($form, $data);
    }

    private function persons(FormInterface $form, array $data): array
    {
        $params = [];
        /** @var PersonRepository $repository */
        $repository = $this->entityManager->getRepository(Person::class);
        foreach ($repository->getByIds($data, $this->language) as $item)
            $params[$item['name']] = $item['id'];

        return array_replace($form->getConfig()->getOptions(), [
            'choices' => $params,
            'data'    => array_values($params),
        ]);
    }

    private function exports(FormInterface $form, array $data): array
    {
        $choices = [];
        foreach (Export::all() as $export)
            $choices[ucfirst($export)] = $export;

        return array_replace($form->getConfig()->getOptions(), [
            'choices' => $choices,
            'data'    => $data,
        ]);
    }

    private function relatedPosts(FormInterface $form, array $data): array
    {
        $params = [];
        /** @var PostRepository $repository */
        $repository = $this->entityManager->getRepository(Post::class);
        foreach ($repository->getByIds($data) as $item)
            $params[$item['title']] = $item['id'];

        return array_replace($form->getConfig()->getOptions(), [
            'choices' => $params,
            'data'    => array_values($params),
        ]);
    }

    private function defaultModification(FormInterface $form, array $data): array
    {
        return array_replace($form->getConfig()->getOptions(), [
            'choices' => array_combine($data, $data),
            'data'    => array_values($data),
        ]);
    }
}