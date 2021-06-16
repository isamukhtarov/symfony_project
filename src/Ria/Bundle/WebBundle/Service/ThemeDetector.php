<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Service;

use Mobile_Detect;
use Ria\Bundle\ThemeBundle\ThemeDetection\ThemeDetectionInterface;

class ThemeDetector implements ThemeDetectionInterface
{
    private const DEVICE_DEFAULT = 'default';
    private const DEVICE_MOBILE  = 'mobile';

    private Mobile_Detect $mobileDetect;

    public function __construct()
    {
        $this->mobileDetect = new Mobile_Detect();
    }

    public function detect(): string
    {
        return $this->detectByDeviceType();
    }

    public function detectByDeviceType(): string
    {
        return ($this->mobileDetect->isMobile() || $this->mobileDetect->isTablet())
            ? self::DEVICE_MOBILE
            : self::DEVICE_DEFAULT;
    }
}