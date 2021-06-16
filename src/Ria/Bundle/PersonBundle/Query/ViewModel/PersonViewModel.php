<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Query\ViewModel;


use Ria\Bundle\CoreBundle\Query\ViewModel;

/**
 * Class PersonViewModel
 * @package Ria\Bundle\CoreBundle\Query\ViewModel
 *
 * @property int $id
 * @property string $slug
 * @property string $first_name
 * @property string $last_name
 * @property string $position
 * @property array photos
 * @property array posts
 */
class PersonViewModel extends ViewModel
{
    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @return string
     */
    public function getTitleWithoutQuotes(): string
    {
        return self::clearDoubleQuotes($this->first_name . ' ' . $this->last_name);
    }

    /**
     * @param string $string
     * @return string
     */
    protected static function clearDoubleQuotes(string $string): string
    {
        return str_replace('"', '', strip_tags($string));
    }
}