<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Helper;

/**
 * Class CategoryHelper
 * @package Ria\Bundle\PostBundle\Helper
 */
class CategoryHelper
{

    public static function pluckChildren(int $parentId, array $child): array
    {
        $ids = [$parentId];
        foreach ($child as $item) {
            $ids[] = $item->id;
        }
        return $ids;
    }

}