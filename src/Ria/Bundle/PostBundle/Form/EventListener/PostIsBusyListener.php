<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\EventListener;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Ria\Bundle\PostBundle\Command\Post\AbstractPostCommand;
use Ria\Bundle\PostBundle\Command\Post\CreatePostCommand;
use Ria\Bundle\PostBundle\Command\Post\UpdatePostCommand;
use Ria\Bundle\PostBundle\Enum\PostIsBusy;
use Ria\Bundle\UserBundle\Entity\Role;
use Ria\Bundle\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;

class PostIsBusyListener implements EventSubscriberInterface
{
    private ?Request $request;

    public function __construct(
        private RedisClient $redisClient,
        private Security $security,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    #[ArrayShape([FormEvents::PRE_SET_DATA => "string", FormEvents::POST_SUBMIT => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA  => 'onPreSetData',
            FormEvents::POST_SUBMIT   => 'onPostSubmit',
        ];
    }

    public function onPreSetData(FormEvent $event): void
    {
        /** @var AbstractPostCommand $command */
        $command = $event->getData();
        /** @var User $user */
        $user = $this->security->getUser();
        $cacheKey = $this->getCacheKey($command);

        if (!$cacheKey) {
            return;
        }

        if ($this->redisClient->exists($cacheKey)) {
            $cacheData = $this->redisClient->hgetall($cacheKey);

            if ($cacheData['id'] != $user->getId() && !$this->isUserSuperAdministrator($user)) {
                throw new AccessDeniedException(
                    sprintf('Access Denied. Post is opened by: %s', $cacheData['name'])
                );
            }
        } else {
            $this->redisClient->hmset($cacheKey, [
                'id'   => $user->getId(),
                'name' => $user->getTranslation($this->request->getLocale())?->getFullName()
            ]);

            $this->redisClient->expire($cacheKey, 3600);
        }
    }

    public function onPostSubmit(FormEvent $event): void
    {
        /** @var UpdatePostCommand $command */
        $command = $event->getData();

        /** @var User $user */
        $user = $this->security->getUser();
        $cacheKey = $this->getCacheKey($command);
        $cacheData = $this->redisClient->hgetall($cacheKey);

        if ($cacheData && $cacheData['id'] == $user->getId()) {
            $this->redisClient->del($cacheKey);
        }
    }

    #[Pure] private function getCacheKey($command): ?string
    {
        if ($command instanceof UpdatePostCommand) {
            return PostIsBusy::getCacheUpdateKey($command->id);
        } elseif ($command instanceof CreatePostCommand && $command->isCreationOfTranslation()) {
            return PostIsBusy::getCacheTranslationKey($command->getPostDto()->getPost()->getId(), $command->language);
        }

        return null;
    }

    private function isUserSuperAdministrator(User $user): bool
    {
        return $user->getRolesRelation()->exists(function ($key, Role $role) {
            return ($role->getName() === 'SuperAdministrator');
        });
    }
}