<?php

namespace Ria\Bundle\ThemeBundle\ThemeDetection;

interface ThemeDetectionInterface
{
    public function detect(): string;
}