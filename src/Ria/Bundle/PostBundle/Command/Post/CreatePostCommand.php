<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Post;

use Ria\Bundle\PostBundle\Dto\PostDto;
use Ria\Bundle\PostBundle\Entity\Post\Status;
use Ria\Bundle\AdminBundle\Command\MetaCommand;
use Ria\Bundle\PhotoBundle\Command\PhotoCommand;

class CreatePostCommand extends AbstractPostCommand
{
    public function __construct(PostDto $postDto)
    {
        $this->createdBy   = $postDto->getUser()->getId();
        $this->authorId    = $this->createdBy;
        $this->publishedAt = date('Y-m-d H:i:s');
        $this->status      = Status::ON_MODERATION;

        if ($postDto->hasPost()) {
            $this->setCreationOfTranslation(true);
            $post = $postDto->getPost();

            $this->id           = $post->getId();
            $this->icon         = $post->getIcon();
            $this->type         = $post->getType()->toValue();
            $this->categoryId   = $post->getCategory()->getId();
            $this->createdBy    = $post->getCreatedBy();
            $this->authorId     = $post->getAuthor()->getId();
            $this->note         = $postDto->getNote()?->getBody();
            $this->expertId     = $post->getExpert()?->getId();
            $this->cityId       = $post->getCity()?->getId();
            $this->storyId      = $post->getStory()?->getId();
            $this->youtubeId    = $post->getYoutubeId();
            $this->status       = Status::CREATED;
            $this->wasPublished = $post->isPublished();
            $this->isActual     = $post->isActual();
            $this->isBreaking   = $post->isBreaking();
            $this->isExclusive  = $post->isExclusive();
            $this->isImportant  = $post->isImportant();
            $this->isMain       = $post->isMain();
            $this->linksNoIndex = $post->isLinksNoindex();
            $this->meta         = new MetaCommand();
            $this->photos       = new PhotoCommand(
                $post->getPhotos()->map(fn($photo) => $photo->getId())->toArray(),
                $post->getPhoto()?->getId(),
            );
        }

        if (!($this->photos instanceof PhotoCommand))
            $this->photos = new PhotoCommand();

        $this->language = $postDto->getLanguage();
        $this->postDto  = $postDto;
    }
}