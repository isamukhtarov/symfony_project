<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Post;

use Ria\Bundle\PostBundle\Dto\PostDto;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\AdminBundle\Command\MetaCommand;
use Ria\Bundle\PhotoBundle\Command\PhotoCommand;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UpdatePostCommand extends AbstractPostCommand
{
    public function __construct(PostDto $postDto)
    {
        $post = $postDto->getPost();

        $this->id = $post->getId();
        $this->language = $post->getLanguage();
        $this->icon = $post->getIcon();
        $this->type = $post->getType()->toValue();
        $this->optionType = $post->getOptionType();
        $this->categoryId = $post->getCategory()->getId();
        $this->createdBy = $post->getCreatedBy();
        $this->authorId = $post->getAuthor()?->getId();
        $this->expertId = $post->getExpert()?->getId();
        $this->translatorId = $post->getTranslator()?->getId();
        $this->cityId = $post->getCity()?->getId();
        $this->storyId = $post->getStory()?->getId();
        $this->youtubeId = $post->getYoutubeId();
        $this->title = $post->getTitle();
        $this->slug = $post->getSlug();
        $this->description = $post->getDescription();
        $this->content = $post->getContent();
        $this->source = $post->getSource();
        $this->publishedAt = $post->getPublishedAt()->format('Y-m-d H:i:s');
        $this->status = $post->getStatus()->toValue();
        $this->markedWords = $post->getMarkedWords();
        // Settings
        $this->wasPublished = $post->isPublished();
        $this->isMain = $post->isMain();
        $this->isExclusive = $post->isExclusive();
        $this->isActual = $post->isActual();
        $this->isBreaking = $post->isBreaking();
        $this->isImportant = $post->isImportant();
        $this->linksNoIndex = $post->isLinksNoIndex();
        $this->note = $postDto->getNote()?->getBody();
        $this->meta = new MetaCommand($post->getMeta());
        $this->photos = new PhotoCommand(
            $post->getPhotos()->map(fn ($photo) => $photo->getId())->toArray(),
            $post->getPhoto()?->getId(),
        );

        $this->fillRelations($post);
        $this->postDto = $postDto;
    }

    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context): void
    {
        $post = $this->getPostDto()->getPost();
        $timestamp = strtotime($this->publishedAt);
        $oldPublishedAt = $post->getPublishedAt()->format('Y-m-d H:i:s');
        if (($timestamp < time()) && $timestamp < strtotime($oldPublishedAt)) {
            $this->publishedAt = $oldPublishedAt;
            $context->buildViolation('You cannot set date to before!')->atPath('publishedAt')->addViolation();
        }
    }

    private function fillRelations(Post $post): void
    {
        foreach ($post->getTags() as $tag)
            $this->tags[] = $tag->getTranslation($this->language)->getName();

        foreach ($post->getPersons() as $person)
            $this->persons[] = $person->getId();

        foreach ($post->getExports() as $export)
            $this->exports[] = $export->getType();

        foreach ($post->getRelatedPosts() as $post)
            $this->relatedPosts[] = $post->getId();
    }
}