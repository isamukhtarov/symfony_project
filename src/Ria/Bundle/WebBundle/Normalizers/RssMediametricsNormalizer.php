<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Normalizers;

use Ria\Bundle\CoreBundle\Helper\TokenHelper;
use Ria\Bundle\PostBundle\Query\ViewModel\PostViewModel;

class RssMediametricsNormalizer extends RssPostNormalizer
{


    protected function getAdditionalFields(PostViewModel $post): array
    {
        return [
            'content' => TokenHelper::clearAll($post->content),
        ];
    }

}