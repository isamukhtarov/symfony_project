<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\EventSubscriber;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Mime\{Address, Email};
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
    ){}

    public function onMessage(MessageEvent $event): void
    {
        $email = $event->getMessage();
        if (!($email instanceof Email)) return;
        $mailerConfig = $this->parameterBag->get('app.mailer');
        foreach ($mailerConfig['admin_emails'] as $address)
            $email->addTo(new Address($address));
        $email->from($mailerConfig['from']);
    }

    #[ArrayShape([MessageEvent::class => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [MessageEvent::class => 'onMessage'];
    }
}