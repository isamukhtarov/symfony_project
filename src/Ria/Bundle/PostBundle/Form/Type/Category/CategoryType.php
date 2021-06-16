<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Type\Category;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Ria\Bundle\PostBundle\Entity\Category\Category;
use Ria\Bundle\PostBundle\Entity\Category\Template;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ria\Bundle\PostBundle\Query\Repository\CategoryRepository;
use Ria\Bundle\PostBundle\Command\Category\UpdateCategoryCommand;
use Symfony\Component\Form\Extension\Core\Type\{
    SubmitType, CheckboxType, ChoiceType, CollectionType,
};

class CategoryType extends AbstractType
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $command = $builder->getData();

        $excludedCategoryId = ($command instanceof UpdateCategoryCommand) ? $command->getCategory()->getId() : null;

        $builder
            ->add('status', CheckboxType::class, [
                'label'    => 'form.status',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit'
            ])
            ->add('template', ChoiceType::class, [
                'label' => 'form.template',
                'choices' => Template::all(),
                'choice_label' => fn ($choice) => $choice
            ])
            ->add('parent', ChoiceType::class, [
                'label' => 'form.parent',
                'placeholder' => '-',
                'required' => false,
                'choices' => $this->categoryRepository->getParentCategoriesWithExcluded($command->getCurrentLocale(), $excludedCategoryId),
                'choice_label' => fn (Category $category) => $category->getTranslation($command->getCurrentLocale())->getTitle(),
                'choice_value' => fn (?Category $category) => $category?->getId(),
            ]);

        $builder->add('translations', CollectionType::class, [
            'entry_type' => CategoryTranslationType::class,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['csrf_protection' => true]);
    }
}