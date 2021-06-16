<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Controller;

use LogicException;
use League\Tactician\CommandBus;
use Ria\Bundle\PostBundle\Dto\PostDto;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Enum\PostIsBusy;
use Ria\Bundle\PostBundle\Enum\PostPreview;
use Ria\Bundle\PostBundle\Form\Grid\PostGrid;
use Ria\Bundle\PostBundle\Messenger\Message\PostCanceled;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Ria\Bundle\VoteBundle\Repository\VoteRepository;
use Ria\Bundle\PostBundle\Entity\Post\{Post, Speech};
use Ria\Bundle\PostBundle\Form\PostRedirectPathGuesser;
use Ria\Bundle\PostBundle\Query\ViewModel\PostViewModel;
use Ria\Bundle\PostBundle\Command\Post\DeletePostCommand;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Ria\Bundle\PostBundle\Service\Archive\PostPhotosArchiveService;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;
use Ria\Bundle\PostBundle\Repository\{PostRepository, NoteRepository};
use Ria\Bundle\CoreBundle\Component\CommandValidator\CommandValidatorInterface;
use Symfony\Component\HttpFoundation\{Cookie, JsonResponse, RedirectResponse, Request, Response};
use Ria\Bundle\PostBundle\Form\Type\Post\{PostCreateType, PostUpdateType, ChangePostDateType};
use Ria\Bundle\PostBundle\Command\Post\{CancelPostCommand,
    CreatePostCommand,
    UpdatePostCommand,
    ArchivePostCommand,
    ChangePostDateCommand,
    PreviewPostCommand};

#[Route('/posts', name: 'posts.')]
class PostController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CommandBus $bus,
        private PostRepository $postRepository,
        private NoteRepository $noteRepository,
        private VoteRepository $votesRepository,
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(PostGrid $grid, Request $request): Response
    {
        if  (($language = $request->get('filter_p_language')) !== null)
            $this->setFilterLanguage($language);

        return $this->render('@RiaPost/posts/index.html.twig', [
            'grid' => $grid->createView(),
            'translators' => $grid->getTranslatorsList()
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, PostRedirectPathGuesser $redirectPathGuesser): Response
    {
        $command = new CreatePostCommand(new PostDto(user: $this->getUser(), language: $request->get('lang')));

        $form = $this->createForm(PostCreateType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            $this->addFlash('success', 'Post created successfully !');
            return $this->redirect($redirectPathGuesser->guess($form));
        }

        return $this->render('@RiaPost/posts/form.html.twig', [
            'form' => $form->createView(),
            'votes' => $this->votesRepository->getVotes($request->get('lang')),
            'cancelRoute' => $this->generateUrl('posts.index', ['filter_p.language' => $request->get('lang')])
        ]);
    }

    #[Route('/create-translation', name: 'create-translation', methods: ['GET', 'POST'])]
    public function createTranslation(Request $request, PostRedirectPathGuesser $redirectPathGuesser): Response
    {
        $post = $this->getPostObject(['parent' => (int) $request->get('parent')]);

        $command = new CreatePostCommand(new PostDto(
            user: $this->getUser(),
            language: $request->get('lang'),
            post: $post,
            note: $this->noteRepository->findOneBy(['postGroupId' => $post->getParent()->getId()])
        ));

        $form = $this->createForm(PostCreateType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            $this->addFlash('success', 'Post created successfully !');
            return $this->redirect($redirectPathGuesser->guess($form));
        }

        return $this->render('@RiaPost/posts/form.html.twig', [
            'form' => $form->createView(),
            'votes' => $this->votesRepository->getVotes($request->get('lang')),
            'cancelRoute' => $this->generateUrl('posts.cancel', ['id' => $post->getParent()->getId(),
                                                                       'language' => $request->get('lang')])
        ]);
    }

    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function update(Request $request, PostRedirectPathGuesser $redirectPathGuesser): Response
    {
        $post = $this->getPostObject(['id' => (int) $request->get('id')]);
        $speech = $this->entityManager->getRepository(Speech::class)->findOneBy(['post' => $post->getId()]);

        $command = new UpdatePostCommand(new PostDto(
            user: $this->getUser(),
            language: $post->getLanguage(),
            post: $post,
            note: $this->noteRepository->findOneBy(['postGroupId' => $post->getParent()->getId()])
        ));

        $form = $this->createForm(PostUpdateType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            $this->addFlash('success', 'Post updated successfully !');
            return $this->redirect($redirectPathGuesser->guess($form, $post));
        }

        return $this->render('@RiaPost/posts/form.html.twig', [
            'post' => $post,
            'speech' => $speech,
            'votes' => $this->votesRepository->getVotes($post->getLanguage()),
            'form' => $form->createView(),
            'cancelRoute' => $this->generateUrl('posts.cancel', ['id' => $post->getId(), 'language' => $post->getLanguage()])
        ]);
    }

    #[Route('/logs/{id}',
        name: 'logs',
        requirements: ['id' => '\d+'],
        methods: ['GET'],
        condition: 'request.isXmlHttpRequest()'
    )]
    public function displayLogs(Post $post): JsonResponse
    {
        return $this->json([
            'status' => true,
            'data' => $this->renderView('@RiaPost/posts/partials/logs.html.twig', [
                'logs' => $post->getLogs()->toArray(),
            ])
        ]);
    }

    #[Route('/delete/{id}',
        name: 'delete',
        requirements: ['id' => '\d+'],
        methods: ['POST'],
        condition: 'request.isXmlHttpRequest()'
      )
    ]
    public function delete(Request $request, CommandValidatorInterface $validator): JsonResponse
    {
        $post = $this->getPostObject(['id' => (int) $request->get('id')]);

        $command = new DeletePostCommand($post, $this->getUser());
        if (!$validator->validate($command, source: $request))
            return $this->json(['status' => false, 'error' => $validator->getErrors()]);
        $this->bus->handle($command);
        return $this->json(['status' => true]);
    }

    #[Route('/change-date/{id}',
        name: 'change-date',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST'],
    )]
    public function changeDate(Request $request): Response
    {
        $command = new ChangePostDateCommand($this->getPostObject(['id' => (int) $request->get('id')]));
        $form = $this->createForm(ChangePostDateType::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->bus->handle($command);
            $this->addFlash('success', 'Date changed successfully.');
            return $this->redirectToRoute('posts.index');
        }

        return $this->render('@RiaPost/posts/change-date.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/archive/{id}',
        name: 'archive',
        requirements: ['id' => '\d+'],
        methods: ['POST'],
        condition: 'request.isXmlHttpRequest()',
    )]
    public function archive(Request $request, CommandValidatorInterface $validator): JsonResponse
    {
        $command = new ArchivePostCommand($this->getPostObject(['id' => (int) $request->get('id')]));
        if (!$validator->validate($command, source: $request))
            return $this->json(['status' => false, 'error' => $validator->getErrors()]);
        $this->bus->handle($command);
        return $this->json(['status' => true]);
    }

    #[Route('/archive-photos/{id}', name: 'archive-photos', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function archivePhotos(int $id, PostPhotosArchiveService $archiveService): Response
    {
        $post = $this->getPostObject(['id' => $id]);
        if (!$post->getType()->isPhoto())
            throw new NotFoundHttpException('The type of post is not valid.');

        $photos = $post->getPhotos();
        if ($photos->isEmpty())
            throw new LogicException('This post has not any photo.');

        $archiveDto = $archiveService->setPost($post)->archive($photos);

        $response = new Response($archiveDto->getStream());
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $archiveDto->getFilename() . '"');
        $response->headers->set('Content-length', $archiveDto->getFileSize());
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 0);

        return $response;
    }

    #[Route('/list', name: 'list', methods: ['GET'], condition: 'request.isXmlHttpRequest()')]
    public function list(Request $request): JsonResponse
    {
        return $this->json($this->postRepository->list(
            $request->get('term'), $request->get('language', $request->getLocale()),
        ));
    }

    #[Route('/preview/{id}', name: 'preview', methods: ['GET'])]
    public function preview(int $id, RedisClient $redisClient, UrlGeneratorInterface $urlGenerator): RedirectResponse
    {
        $command = PreviewPostCommand::fromExisting($id);
        $this->bus->handle($command);

        if (!$redisClient->exists(PostPreview::getCacheKey($command->key))) {
            $this->createNotFoundException('No key in redis');
        }

        /** @var PostViewModel $postViewModel */
        $postViewModel = unserialize($redisClient->get(PostPreview::getCacheKey($command->key)));

        return $this->redirect($urlGenerator->generate('post.preview', [
            'categorySlug' => $postViewModel->category_slug,
            'slug' => $postViewModel->slug,
            'key' => $command->key
        ]));
    }

    #[Route('/preview/create', name: 'preview.create', methods: ['POST'], condition: 'request.isXmlHttpRequest()')]
    public function createPreview(Request $request, RedisClient $redisClient, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $command = PreviewPostCommand::fromDto(new PostDto(user: $this->getUser(), language: $request->get('lang')));

        $formType = $request->get('form_type') == 'post_update' ? PostUpdateType::class : PostCreateType::class;
        $form = $this->createForm($formType, $command);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->json(['success' => false, 'error' => 'Validation error']);
        }

        $this->bus->handle($command);

        if (!$redisClient->exists(PostPreview::getCacheKey($command->key))) {
            return $this->json(['success' => false, 'error' => 'No key in redis']);
        }

        /** @var PostViewModel $postViewModel */
        $postViewModel = unserialize($redisClient->get(PostPreview::getCacheKey($command->key)));

        $url = $urlGenerator->generate('post.preview', [
            'categorySlug' => $postViewModel->category_slug,
            'slug' => $postViewModel->slug,
            'key' => $command->key
        ]);

        return $this->json(['success' => true, 'data' => $url]);
    }

    #[Route('/unblock/{id}', name: 'unblock', methods: ['GET'])]
    public function unblock(string $id, RedisClient $redisClient): RedirectResponse
    {
        $cacheKey = PostIsBusy::getCacheUpdateKey($id);

        if ($redisClient->exists($cacheKey))
            $redisClient->del($cacheKey);

        return $this->redirectToRoute('posts.index');
    }

    #[Route('/unblock/{parentId}/{language}', name: 'unblock.translation-create', methods: ['GET'])]
    public function unblockTranslationCreate(int $parentId, string $language, RedisClient $redisClient): RedirectResponse
    {
        $cacheKey = PostIsBusy::getCacheTranslationKey($parentId, $language);

        if ($redisClient->exists($cacheKey))
            $redisClient->del($cacheKey);

        return $this->redirectToRoute('posts.index');
    }

    private function getPostObject(array $condition): Post
    {
        /** @var Post $post */
        if (($post = $this->postRepository->findOneBy($condition)) === null)
            throw new NotFoundHttpException('Post not found !');
        return $post;
    }

    #[Route('/cancel/{id}/{language}', name: 'cancel')]
    public function makeCancel(int $id, string $language, RedisClient $redisClient): Response
    {
        $post = $this->getPostObject(['id' => $id]);
        $user = $this->getUser();
        $this->bus->handle(new CancelPostCommand($post, $user));

        $updateCacheKey = PostIsBusy::getCacheUpdateKey($id);
        if ($redisClient->exists($updateCacheKey))
            $redisClient->del($updateCacheKey);

        $translationCacheKey = PostIsBusy::getCacheTranslationKey($id, $language);
        if ($redisClient->exists($translationCacheKey))
            $redisClient->del($translationCacheKey);

        return $this->redirectToRoute('posts.index', ['filter_p.language' => $language]);
    }


    private function setFilterLanguage(string $language): Response
    {
        $cookie = new Cookie(
            name: 'postFilterLanguage',
            value: $language,
            expire: new \DateTime('+1 month'),
            path: '/',
            domain: null,
            secure: false,
            httpOnly: false,
        );

        $response = new Response();
        $response->headers->setCookie($cookie);
        $response->sendHeaders();

        return $response;
    }
}