<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Console\Command;

use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\PostBundle\Entity\Category\Category;
use Ria\Bundle\PostBundle\Entity\Category\Template;
use Ria\Bundle\PostBundle\Entity\Category\Translation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCategoriesCommand extends OldDataMigrationCommand
{

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $groups = $this->getGroups();

        foreach ($groups as $i => $group) {
            $categories = $this->getCategoriesByGroup($group);

            $parentCategoryId = $this->createCategory($categories);

            $sortedChildCategories = $this->getChildCategories(array_values(array_map(function ($category) {
                return $category['id'];
            }, $categories)));

            foreach ($sortedChildCategories as $childCategories) {
                $this->createCategory($childCategories, $parentCategoryId);
            }

            $this->renderStatus($i + 1, count($groups));
        }

        return Command::SUCCESS;
    }

    protected function getGroups(): array
    {
        return $this->oldEntityManager->getConnection()->executeQuery("SELECT c.group 
                FROM rp_categories c
                WHERE c.parent_id IS NULL
                GROUP BY c.group")
            ->fetchFirstColumn();
    }

    protected function getCategoriesByGroup(string $group): array
    {
        return $this->oldEntityManager->getConnection()
            ->executeQuery("SELECT c.*,
                cm.title as meta_title, cm.description as meta_description,
                cm.keywords as meta_keywords
                FROM rp_categories c
                LEFT JOIN rp_category_meta cm ON cm.category_id = c.id
                WHERE c.group = :group", ['group' => $group])
            ->fetchAllAssociative();
    }

    private function getChildCategories(array $parentIds): array
    {
        $categories = [];
        foreach ($parentIds as $parentId) {
            $categories = array_merge($this->oldEntityManager->getConnection()->executeQuery("SELECT c.*,
                                cm.title as meta_title, cm.description as meta_description,
                                cm.keywords as meta_keywords
                                FROM rp_categories c
                                LEFT JOIN rp_category_meta cm ON cm.category_id = c.id
                                WHERE c.parent_id = :parent
                            ", [':parent' => $parentId])
                ->fetchAllAssociative(), $categories);
        }
        return $this->getSortedCategories($categories);
    }

    private function getSortedCategories(array $records): array
    {
        $categoriesByLanguages = [];
        foreach ($records as $record) {
            $categoriesByLanguages[$record['group']][] = $record;
        }
        return array_values($categoriesByLanguages);
    }

    private function createCategory(array $translates, ?int $parentId = null): int
    {
        $model = new Category();
        $model
            ->setStatus(true)
            ->setSort((int)$translates[0]['order'])
            ->setTemplate(new Template(Template::DEFAULT))
        ;

        if (!empty($parentId)) {
            $model->setParent(
                $this->entityManager->getReference(Category::class, $parentId)
            );
        }

        foreach ($translates as $category) {
            $translation     = new Translation();
            $translation
                ->setLanguage($category['lang'])
                ->setTitle($category['name'])
                ->setSlug($category['url'])
                ->setMeta(new Meta(
                    $category['meta_title'] ?? '',
                    $category['meta_description'] ?? '',
                    $category['meta_keywords'] ?? '',
                ))
            ;

            $model->addTranslation($translation);
        }

        $this->entityManager->persist($model);
        $this->entityManager->flush();

        foreach ($translates as $category) {
            $this->createConformity($category['id'], $model->getId(), 'category');
        }

        return $model->getId();
    }

}