<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Grid;

use Doctrine\ORM\QueryBuilder;
use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\DataGridBundle\Grid\Grid;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Entity\Category\Category;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Entity\Post\{Status, Note};
use Symfony\Contracts\Translation\TranslatorInterface;
use Ria\Bundle\DataGridBundle\Grid\{GridConfig, GridManager};
use Symfony\Component\HttpFoundation\{Cookie, RequestStack, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Ria\Bundle\PostBundle\Form\Grid\Column\{PostStatusColumn, PostTranslationColumn, PostIsBusyColumn};

class PostGrid
{
    private ?Request $request;
    private string $locale;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private PostRepository $postRepository,
        private GridManager $gridManager,
        private TranslatorInterface $translator,
        private PostStatusColumn $statusColumn,
        private PostTranslationColumn $translationColumn,
        private ParameterBagInterface $parameterBag,
        private UrlGeneratorInterface $urlGenerator,
        private PostIsBusyColumn $postIsBusyColumn,
        private RequestStack $requestStack,
    )
    {
        $this->request = $requestStack->getMasterRequest();
        $this->locale  = $this->request->getLocale();
    }

    public function createView(): Grid
    {
        $gridConfig = (new GridConfig())
            ->setQueryBuilder($this->getPostQuery())
            ->setCountFieldName('p.id')
            ->addField('p.id', [
                'label'      => 'Id',
                'sortable'   => true,
                'filterable' => true,
                'attr'       => ['style' => 'width:120px'],
            ])
            ->addField('p.title', [
                'label'               => 'Title',
                'filterable'          => true,
                'formatValueCallback' => fn(string $value, array $row) => $this->formatTitle($value, $row),
                'autoEscape'          => false,
            ])
            ->addField('c.id', [
                'label'               => 'form.category',
                'formatValueCallback' => fn($value, $row) => $row['ct.title'],
                'filterable'          => true,
                'filterData'          => $this->getCategoriesList()
            ])
            ->addField('a.id', [
                'label'               => 'form.author',
                'uniqueId'            => 'author',
                'formatValueCallback' => fn($value, $row) => $row['at.firstName'] . ' ' . $row['at.lastName'],
                'filterable'          => true,
                'filterData'          => $this->getAuthorsList()
            ])
            ->addField('p.status.status', [
                'label'               => 'Status',
                'formatValueCallback' => fn($value, $row) => $this->statusColumn->render($row['p.status.status']),
                'filterable'          => true,
                'filterData'          => $this->getStatusesList(),
                'autoEscape'          => false,
            ])
            ->addField('note', [
                'label'               => 'form.note',
                'formatValueCallback' => fn($value, $row) => $row['nt.body'],
            ])
            ->addField('busy', [
                'label'               => 'Busy',
                'formatValueCallback' => fn($value, $row) => $this->postIsBusyColumn->render($row['p.id'], (int)$row['postParentId']),
                'autoEscape'          => false,
            ])
            ->addField('p.publishedAt', [
                'label'      => 'Datetime',
                'filterable' => true,
                'filterAttr' => ['data-plugin' => 'datetimepicker', 'autocomplete' => 'off']
                // 'formatValueCallback' => fn ($value) => $value->format('yyyy-mm-dd'),
            ])
            ->addField('audio', [
                'label' => 'Audio',
            ])
            ->addField('translations', [
                'label'               => 'Translations',
                'formatValueCallback' => fn($value, $row) => $this->translationColumn->render($row['p.id']),
                'autoEscape'          => false,
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request);
    }

    private function getCategoriesList(): array
    {
        /** @var Category[] $categories */
        $categories = $this->entityManager
            ->getRepository(Category::class)
            ->getAll($this->locale);

        $tree = [];
        foreach ($categories as $category) {
            $tree[$category->getId()] = $category->getTranslation($this->locale)->getTitle();

            foreach ($category->getChildren() as $child) {
                $tree[$child->getId()] = ' - ' . $child->getTranslation($this->locale)->getTitle();
            }
        }

        return $tree;
    }

    private function formatTitle(string $value, array $row): string
    {
        $route      = null;
        $additional = [];

        $isFuturePost    = $row['p.publishedAt']->getTimestamp() > time();
        $isPrivatePost   = $row['p.status.status'] == Status::PRIVATE;

        if (empty($row['photoId'])) {
            $additional[] = $this->translator->trans('without photo');
        }
        if ($row['p.isPublished']) {
            $route = 'post_view';
        } elseif ($isPrivatePost) {
            $route = 'post_private';
        } elseif ($isFuturePost || in_array($row['p.status.status'], Status::publishedOnes())) {
            $route        = 'post_future';
            $additional[] = $this->translator->trans('for future');
        }

        if (empty($route)) {
            return $value;
        }

        $postUrl = $this->urlGenerator->generate($route, [
            'categorySlug' => $row['ct.slug'], 'slug' => $row['p.slug'], '_locale' => $row['p.language'],
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return "<a href=\"{$postUrl}\" target=\"blank\">{$value}</a>"
            . (empty($additional) ? '' : (' <sup>' . implode(' ' . $this->translator->trans('and') . ' ', $additional) . '</sup>'));
    }

    private function getAuthorsList(): array
    {
        /** @var User[] $authors */
        $authors = $this->entityManager
            ->getRepository(User::class)
            ->getByPermission('canBeAuthor', $this->locale);

        $result = [];
        foreach ($authors as $author) {
            $result[$author->getId()] = $author->getTranslation($this->locale)->getFullName();
        }

        return $result;
    }

    private function getStatusesList(): array
    {
        $data = [];
        foreach (Status::all() as $status) {
            $data[$status] = $this->translator->trans($status);
        }

        return $data;
    }

    public function getTranslatorsList(): array
    {
        /** @var User[] $translators */
        $translators = $this->entityManager
            ->getRepository(User::class)
            ->getByRoles(['Translator', 'LeadTranslator']);

        $result = [];
        foreach ($translators as $translator) {
            $result[$translator->getId()] = $translator->getTranslation($this->locale)->getFullName();
        }

        return $result;
    }

    private function getPostQuery(): QueryBuilder
    {
        $queryBuilder = $this->postRepository
            ->createQueryBuilder('p')
            ->select('p', 'IDENTITY(p.parent) as postParentId',
                'p.type.type', 'IDENTITY(p.photo) as photoId', 'partial c.{id}', 'partial ct.{id, slug, title}',
                'partial a.{id}', 'partial at.{id, firstName, lastName}', 'partial ptr.{id}', 'partial nt.{id, body}'
            )
            ->innerJoin('p.category', 'c')
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :language')
            ->innerJoin('p.author', 'a')
            ->leftJoin('p.translator', 'ptr')
            ->innerJoin('a.translations', 'at', 'WITH', 'at.language = :language')
            ->leftJoin(Note::class, 'nt', 'WITH', 'nt.postGroupId = p.parent')
            ->orderBy('p.id', 'DESC')
            ->setParameter('language', $this->request->getLocale());

        if ($this->request->get('filter_p_language') === null)
            $queryBuilder
                ->andWhere('p.language = :locale')
                ->setParameter(
                    'locale',
                    $this->request->cookies->get('postFilterLanguage') ?? $this->parameterBag->get('app.locale')
                );
        return $queryBuilder;
    }
}