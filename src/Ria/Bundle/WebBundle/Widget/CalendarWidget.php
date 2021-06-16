<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Ria\Bundle\CoreBundle\Web\FrontendWidget;

class CalendarWidget extends FrontendWidget
{

    public function  run(): string
    {
        return $this->render($this->template);
    }
}