<?php

declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\Security;

use JetBrains\PhpStorm\ArrayShape;
use Ria\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfToken;
use Ria\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\{Request, RedirectResponse, Response};
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

/**
 * Class AppAuthenticator
 * @package Ria\Bundle\AdminBundle\Security
 */
class AppAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'auth.login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private UserPasswordEncoderInterface $passwordEncoder,
        private UserRepository $userRepository,
    ){}

    public function supports(Request $request): bool
    {
        return (self::LOGIN_ROUTE === $request->attributes->get('_route') && $request->isMethod('POST'));
    }

    #[ArrayShape(['email' => "mixed", 'password' => "mixed", 'csrf_token' => "mixed"])]
    public function getCredentials(Request $request): array
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(Security::LAST_USERNAME, $credentials['email']);
        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token))
            throw new InvalidCsrfTokenException();

        /** @var User $user */
        if (!($user = $this->userRepository->findOneBy(['email' => $credentials['email']])))
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey))
            return new RedirectResponse($targetPath);
        return new RedirectResponse($this->urlGenerator->generate('admin.index'));
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
