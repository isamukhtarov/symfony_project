<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Hydrator;

use Doctrine\ORM\EntityManagerInterface;
use PDO;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\CoreBundle\Query\ViewModelHydrator;
use Ria\Bundle\PostBundle\Query\ViewModel\CategoryViewModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryHydrator extends ViewModelHydrator
{
    const HYDRATION_MODE = 'category_hydrator';

//    public function __construct(protected EntityManagerInterface $entityManager, protected ContainerInterface $container)
//    {
//        parent::__construct($entityManager);
//    }

    protected function getViewModelClassName(): string
    {
        return CategoryViewModel::class;
    }

    protected function hydrateAllData(): array
    {
        $result = [];

        while ($row = $this->_stmt->fetchAssociative(PDO::FETCH_ASSOC)) {
            $this->hydrateRowData($row, $result);

            $className  = $this->getViewModelClassName();
            $lastResult = end($result);

            if (array_key_exists('meta.meta', $lastResult)) {
                $metaAsArray = json_decode($lastResult['meta.meta'], true);
                $lastResult['meta'] = new Meta(
                    $metaAsArray['title'],
                    $metaAsArray['description'],
                    $metaAsArray['keywords']
                );
                unset($lastResult['meta.meta']);
            }

//            $parentColumns = [];
//            foreach ($lastResult as $column => $value) {
//                if (str_contains($column, 'parent_') && !is_null($value)) {
//                    $parentColumns[str_replace('parent_', '', $column)] = $value;
//                    unset($lastResult[$column]);
//                }
//            }

//            if (!empty($parentColumns)) {
//                $lastResult['parent'] = Yii::$container->get($className, [], $parentColumns);
//            }

//            $categoryViewModel = $this->container->get($className);
//            $categoryViewModel->setData($lastResult);
//            $result[array_key_last($result)] = $categoryViewModel;

            $result[array_key_last($result)] = new $className($lastResult);
        }

        return $result;
    }
}
