<?php

namespace Ria\Bundle\CoreBundle\Query;

use Doctrine\ORM\Internal\Hydration\ArrayHydrator;
use Ria\Bundle\CoreBundle\Entity\Meta;
use PDO;

abstract class ViewModelHydrator extends ArrayHydrator
{
    abstract protected function getViewModelClassName(): string;

    protected function hydrateAllData()
    {
        $result = [];

        while ($row = $this->_stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->hydrateRowData($row, $result);

            $className = $this->getViewModelClassName();
            $lastResult = end($result);

            if (isset($lastResult['content.content'])) {
                $lastResult['content'] = $lastResult['content.content'];
                unset($lastResult['content.content']);
            }

            if (array_key_exists('meta', $lastResult)) {
                $metaAsArray = json_decode($lastResult['meta'], true);
                $lastResult['meta'] = new Meta(
                    $metaAsArray['title'],
                    $metaAsArray['description'],
                    $metaAsArray['keywords']
                );
            }

            $result[array_key_last($result)] = new $className($lastResult);
        }

        return $result;
    }
}