<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\ViewModel;

use Ria\Bundle\CoreBundle\Query\ViewModel;
use Ria\Bundle\PostBundle\Entity\Widget\Type;

/**
 * Class WidgetViewModel
 * @package Ria\Bundle\PostBundle\Query\ViewModel
 *
 * @property int id
 * @property string type
 * @property string content
 */
class WidgetViewModel extends ViewModel
{

    public function isYoutube(): bool
    {
        return $this->type == Type::YOUTUBE;
    }

    public function isInstagram(): bool
    {
        return $this->type == Type::INSTAGRAM;
    }

}