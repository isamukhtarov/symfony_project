<?php

declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\EventListener;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LanguageRequestListener
{
    public function __construct(
        private TranslatorInterface $translator,
        private ParameterBagInterface $parameterBag,
    ){}

    #[NoReturn] public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) return;

        if (($cookieLocale = $event->getRequest()->cookies->get('language')) !== null) {
            $event->getRequest()->setLocale($cookieLocale);
            $this->translator->setLocale($cookieLocale);
        }

    }
}
