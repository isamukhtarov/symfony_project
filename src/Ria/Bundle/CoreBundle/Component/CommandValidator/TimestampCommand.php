<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Component\CommandValidator;

use Ria\Bundle\CoreBundle\Validation\Constraint\Timestamp;

/**
 * Class TimestampCommand
 * @package Ria\Bundle\CoreBundle\Component\CommandValidator
 */
class TimestampCommand implements Validatable
{

    #[Timestamp]
    public $timestamp;

}