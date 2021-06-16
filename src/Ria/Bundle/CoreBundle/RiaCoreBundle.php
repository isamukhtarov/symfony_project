<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class RiaCoreBundle extends Bundle
{
    public function boot()
    {
        date_default_timezone_set("Asia/Baku"); // You can extract timezone to parameters.
    }
}