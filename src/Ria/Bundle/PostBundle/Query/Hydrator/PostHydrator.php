<?php

namespace Ria\Bundle\PostBundle\Query\Hydrator;

use Doctrine\ORM\Internal\Hydration\HydrationException;
use PDO;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\CoreBundle\Query\ViewModelHydrator;
use Ria\Bundle\PostBundle\Entity\Category\Category;
use Ria\Bundle\PostBundle\Query\ViewModel\PostViewModel;

class PostHydrator extends ViewModelHydrator
{
    const HYDRATION_MODE = 'post_hydrator';

    /**
     * @return string
     */
    protected function getViewModelClassName(): string
    {
        return PostViewModel::class;
    }

    /**
     * @return array
     * @throws HydrationException
     */
    protected function hydrateAllData()
    {
        $result = [];

        while ($row = $this->_stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->hydrateRowData($row, $result);

            $className = $this->getViewModelClassName();
            $lastResult = end($result);

            if (isset($lastResult['type.type'])) {
                $lastResult['type'] = $lastResult['type.type'];
                unset($lastResult['type.type']);
            }

            if (isset($lastResult['status.status'])) {
                $lastResult['status'] = $lastResult['status.status'];
                unset($lastResult['status.status']);
            }

            if (isset($lastResult['content.content'])) {
                $lastResult['content'] = $lastResult['content.content'];
                unset($lastResult['content.content']);
            }

            if (array_key_exists('meta.meta', $lastResult)) {
                $metaAsArray = json_decode($lastResult['meta.meta'], true);
                $lastResult['meta'] = new Meta(
                    $metaAsArray['title'],
                    $metaAsArray['description'],
                    $metaAsArray['keywords']
                );
                unset($lastResult['meta.meta']);
            }

            $result[array_key_last($result)] = new $className($lastResult);
        }

        if (!(isset($result[0]) && isset($result[0]->category_id))) {
            return $result;
        }

        $language = $result[0]->language;
        $categoryIds = collect($result)->pluck('category_id')->toArray();
        $categories = collect(
            $this->_em->getRepository(Category::class)->getForPosts($categoryIds, $language)
        );

        foreach ($result as &$post) {
            $postCategory = $categories->where('category_id', $post->category_id)->first();
            $post->category_slug = $postCategory->category_slug;
            $post->category_title = $postCategory->category_title;
        }

        return $result;
    }
}