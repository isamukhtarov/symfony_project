<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Normalizers;

use Ria\Bundle\PostBundle\Query\ViewModel\PostViewModel;

/**
 * Class RssMainNormalizer
 * @package Ria\Bundle\WebBundle\Normalizers
 */
class RssMainNormalizer extends RssPostNormalizer
{

    protected function getAdditionalFields(PostViewModel $post): array
    {
        return [];
    }

}