<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Post;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\PostBundle\Command\Post\PreviewPostCommand;
use Ria\Bundle\PostBundle\Entity\Category\Category;
use Ria\Bundle\PostBundle\Enum\PostPreview;
use Ria\Bundle\PostBundle\Query\ViewModel\PostViewModel;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;

class PreviewPostHandler
{
    private ?Request $request;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RedisClient $client,
        private PostRepository $postRepository,
        RequestStack $requestStack
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    public function handle(PreviewPostCommand $command)
    {
        $cacheKey = PostPreview::getCacheKey($command->key);

        $data = $command->id
            ? $this->postRepository->getById($command->id, $this->request->getLocale())
            : $this->transformCommandToViewModel($command);

        $this->client->set($cacheKey, serialize($data));
        $this->client->expire($cacheKey, 3600);
    }

    private function transformCommandToViewModel(PreviewPostCommand $command): PostViewModel
    {
        $category = $this->entityManager->find(Category::class, $command->categoryId);
        $author   = $this->entityManager->find(User::class, $command->authorId);

        return new PostViewModel([
            'id'                => 0,
//            'group_id'          => $command->groupId,
            'category_id'       => $command->categoryId,
            'title'             => $command->title,
            'description'       => $command->description,
            'slug'              => $command->slug,
//            'marked_words'      => $command->marked_words,
            'type'              => $command->type,
            'status'            => $command->status,
            'icon'              => $command->icon,
            'content'           => (string)$command->content,
            'language'          => $command->language,
            'category_title'    => $category->getTranslation($command->language)->getTitle(),
            'category_slug'     => $category->getTranslation($command->language)->getSlug(),
            'isPublished'      => true,
            'publishedAt'      => new DateTime($command->publishedAt),
            'createdAt'        => new DateTime(),
            'updatedAt'        => new DateTime(),
//            'image'             => $command->image,
            'youtube_id'        => $command->youtubeId,
            'isMain'           => $command->isMain,
            'isExclusive'      => $command->isExclusive,
            'isActual'         => $command->isActual,
            'isBreaking'       => $command->isBreaking,
            'views'             => 0,
            'author_name'       => $author->getTranslation($command->language)->getFullName(),
            'author_first_name' => $author->getTranslation($command->language)->getFirstName(),
            'author_last_name'  => $author->getTranslation($command->language)->getLastName(),
            'author_slug'       => $author->getTranslation($command->language)->getSlug(),
            'author_thumb'      => $author->getImage(),
            'tags'              => $command->tags,
            'meta'              => new Meta(...(array) $command->meta),
            'photoId'           => $command->getPostDto()?->getPost()?->getPhoto()?->getId(),
            'speech_filename'   => null,
            'linksNoIndex'      => true,
        ]);
    }

}