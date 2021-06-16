<?php

declare(strict_types=1);

namespace Ria\Bundle\ThemeBundle\EventListener;

use JetBrains\PhpStorm\NoReturn;
use Ria\Bundle\ThemeBundle\ActiveTheme;
use Symfony\Component\HttpFoundation\Cookie;
use Ria\Bundle\ThemeBundle\ThemeDetection\ThemeDetectionInterface;
use Symfony\Component\HttpKernel\Event\{RequestEvent, ResponseEvent};

class ThemeRequestListener
{
    protected string|null $newTheme = null;

    public function __construct(
        protected ActiveTheme $activeTheme,
        protected ThemeDetectionInterface $themeDetect,
        protected array|null $cookieOptions = null,
    ){}

    #[NoReturn] public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) return;

        if (!($cookieValue = $event->getRequest()->cookies->get($this->cookieOptions['name']))) {
            $cookieValue = $this->themeDetect->detect();
            $this->newTheme = $cookieValue;
        }

        $this->activeTheme->setCurrentTheme($cookieValue);
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMasterRequest() || !$this->newTheme) return;

        $event->getResponse()->headers->setCookie(new Cookie(
            name: $this->cookieOptions['name'],
            value: $this->newTheme,
            expire: (time() + $this->cookieOptions['lifetime']),
            path: $this->cookieOptions['path'],
            domain: $this->cookieOptions['domain'],
            secure: (bool) $this->cookieOptions['secure'],
            httpOnly: (bool) $this->cookieOptions['http_only'],
        ));

        $event->getResponse()->sendHeaders();
    }
}
