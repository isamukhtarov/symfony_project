<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Component\ViewsCounter\Collector;

/**
 * Class BaseCollector
 * @package Ria\Bundle\CoreBundle\Component\ViewsCounter\Collector
 */
class BaseCollector extends Collector
{

    public function collect(): array
    {
        $viewsCollections = [];
        $languages        = implode('|', $this->parameterBag->get('app.supported_locales'));

        $records = $this->isFakeDataPreferred() ? $this->getFakeData() : $this->redisClient->keys('views_*');

        foreach ($records as $hash) {
            if (!preg_match('/views\_([0-9]+)\_(' . $languages . ')\_(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\_([a-zA-Z0-9]{32})\_[0-9]{10}/', $hash, $matches)) {
                continue;
            }

            [$detectedHash, $postId, $lang, $userIp, $md5UA] = $matches;

            $views = (int)$this->redisClient->get($hash);
            $views = empty($views) ? 1 : $views;

            if (isset($viewsCollections[$postId . '_' . $lang])) {
                $viewsCollections[$postId . '_' . $lang] += $views;
            } else {
                $viewsCollections[$postId . '_' . $lang] = $views;
            }

            $this->redisClient->del($hash);
        }

        return $viewsCollections;
    }

    public function getFakeData(): array
    {
        return [
            'views_21235_ge_93.184.238.126_12eb5fc18ed4e05474bc1cefb051fac3_2019060721',
            'views_21235_ge_93.184.238.128_12eb5fc18ed4e05474bc1cefb051fac3_2019060721',
        ];
    }

}