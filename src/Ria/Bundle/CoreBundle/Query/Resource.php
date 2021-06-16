<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Query;

abstract class Resource
{
    public abstract function toArray(mixed $item): array;

    public function transform(mixed $resource): array
    {
        if (is_array($resource)) {
            return array_map(function ($item) {
                return $this->toArray($item);
            }, $resource);
        }

        return $this->toArray($resource);
    }

}