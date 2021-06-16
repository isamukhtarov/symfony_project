<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Post;

use Ria\Bundle\PostBundle\Dto\PostDto;
use Ria\Bundle\AdminBundle\Command\MetaCommand;
use Ria\Bundle\PhotoBundle\Command\PhotoCommand;

class PreviewPostCommand extends AbstractPostCommand
{
    public ?int $id = null;
    public ?string $key;

    public static function fromExisting(int $id): self
    {
        $object = new self();
        $object->key = $object->generateRandomKey();
        $object->id = $id;
        return $object;
    }

    public static function fromDto(PostDto $postDto): self
    {
        $object = new self();
        $object->key = $object->generateRandomKey();
        $object->createdBy = $postDto->getUser()->getId();
        $object->authorId = $object->createdBy;
        $object->publishedAt = date('Y-m-d H:i:s');

        if ($postDto->hasPost()) {
            $object->setCreationOfTranslation(true);

            $post = $postDto->getPost();
            $object->groupId = $post->getId();
            $object->icon = $post->getIcon();
            $object->type = $post->getType()->toValue();
            $object->categoryId = $post->getCategory()->getId();
            $object->createdBy = $post->getCreatedBy();
            $object->authorId = $post->getAuthor()->getId();
            $object->note = $postDto->getNote()?->getBody();
            $object->expertId = $post->getExpert()?->getId();
            $object->cityId = $post->getCity()->getId();
            $object->storyId = $post->getStory()?->getId();
            $object->youtubeId = $post->getYoutubeId();
            $object->publishedAt = date('Y-m-d H:i:s');
            $object->isActual = $post->isActual();
            $object->isBreaking = $post->isBreaking();
            $object->isExclusive = $post->isExclusive();
            $object->isImportant = $post->isImportant();
            $object->isMain = $post->isMain();
            $object->linksNoIndex = $post->isLinksNoindex();
            $object->meta = new MetaCommand($post->getMeta());
            $object->photos = new PhotoCommand(
                $post->getPhotos()->map(fn ($photo) => $photo->getId())->toArray(),
                $post->getPhoto()?->getId(),
            );
        }

        if (!($object->photos instanceof PhotoCommand))
            $object->photos = new PhotoCommand();

        $object->language = $postDto->getLanguage();
        $object->postDto = $postDto;

        return $object;
    }

    private function generateRandomKey(): string
    {
        return sha1(random_bytes(10));
    }
}