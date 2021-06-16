<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use Ria\Bundle\CoreBundle\Component\CommandValidator\CommandValidatorInterface;
use Ria\Bundle\CoreBundle\Component\CommandValidator\TimestampCommand;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\UserBundle\Entity\Translation;
use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\UserBundle\Query\ViewModel\UserViewModel;
use Ria\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthorController extends AbstractController
{

    protected const POSTS_COUNT = 20;

    public function __construct(
        protected UserRepository $userRepository,
        protected PostRepository $postRepository
    )
    {
    }

    #[Route("/author/{slug}/", name: "author_view", methods: ['GET'], priority: 2)]
    public function view(Request $request, string $slug): Response
    {
        $author = $this->getAuthor($slug, $request->getLocale());

        $urlTranslations = $this->getUrlTranslations($author->id);
        $posts           = $this->postRepository->getByAuthor($author->id, $request->getLocale(), self::POSTS_COUNT);

        return $this->render('@RiaWeb/index/author.html.twig', compact(
            'author',
            'posts',
            'urlTranslations'
        ));
    }

    #[Route("/author-ajax/{authorId<\d+>}/", name: "author_ajax", methods: ['GET'], priority: 2)]
    public function authorAjax(Request $request, CommandValidatorInterface $validator, int $authorId): JsonResponse
    {
        $timestamp = $request->get('timestamp');
        if (!$validator->validate(new TimestampCommand(), source: $request)) {
            throw $this->createNotFoundException();
        }

        $posts = $this->postRepository->getByAuthor($authorId, $request->getLocale(), self::POSTS_COUNT, $timestamp);

        return new JsonResponse([
            'html'         => $this->renderView('@RiaWeb/index/partials/_category-post-item.html.twig', compact('posts')),
            'limitReached' => count($posts) < self::POSTS_COUNT
        ]);
    }

    protected function getAuthor(string $slug, string $language): UserViewModel
    {
        $author = $this->userRepository->getBySlug($slug, $language);

        if (empty($author) || !$this->userIsAuthor($author->id)) {
            throw $this->createNotFoundException();
        }

        return $author;
    }

    protected function userIsAuthor(int $userId): bool
    {
        return (bool)$this->getDoctrine()
            ->getManager()
            ->getConnection()
            ->executeQuery('
                    SELECT COUNT(user_id) 
                    FROM user_permission up
                    INNER JOIN permissions p ON p.id = up.permission_id AND p.name = :permission
                    WHERE user_id = :user',
                ['user' => $userId, 'permission' => 'canBeAuthor']
            )->fetchColumn();
    }

    protected function getUrlTranslations(int $authorId): array
    {
        /** @var User $user */
        $user = $this->userRepository->find($authorId);

        $availableUrls = [];
        /** @var Translation $translation */
        foreach ($user->getTranslations() as $translation) {
            $availableUrls[$translation->language] = $this->generateUrl('author_view',
                ['slug' => $translation->getSlug(), '_locale' => $translation->getLanguage()],
                UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $urlTranslations = [];
        foreach ($this->getParameter('app.supported_locales') as $language) {
            $urlTranslations[$language] = $availableUrls[$language] ?? null;
        }
        return $urlTranslations;
    }

}