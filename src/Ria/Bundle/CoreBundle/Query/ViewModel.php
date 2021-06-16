<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Query;

use ReflectionClass;

abstract class ViewModel
{
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value)
            $this->$key = $value;
    }

    public function toArray(): array
    {
        $data = [];
        $reflection = new ReflectionClass($this);

        preg_match_all('/@property\s+[^\s]++\s+.?(?P<properties>[^\s]+)/', $reflection->getDocComment(), $matches);

        foreach($reflection->getProperties() as $property) {
            $data[$property->getName()] = $property->getValue($this);
        }

        foreach ($matches['properties'] as $docProperty) {
            if (isset($this->{$docProperty})) {
                $data[$docProperty] = $this->{$docProperty};
            }
        }

        return $data;
    }

    public function __get(string $name)
    {
        return $this->$name;
    }
}