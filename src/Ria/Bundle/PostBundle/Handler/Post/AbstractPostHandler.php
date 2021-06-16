<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Post;

use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\ORMException;
use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use Ria\Bundle\PhotoBundle\Command\PhotoCommand;
use Doctrine\Common\Collections\ArrayCollection;
use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Ria\Bundle\PostBundle\Command\Post\AbstractPostCommand;
use Ria\Bundle\PostBundle\Repository\{PostRepository, NoteRepository};
use Ria\Bundle\PostBundle\Entity\Post\{Post, Export, Note, PostPhoto, Type, Status};
use Ria\Bundle\PostBundle\Entity\{City\City, Story\Story, Category\Category, Tag\Tag, Tag\Translation as TagTranslation};

abstract class AbstractPostHandler
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected MessageBusInterface $messageBus,
        protected PostRepository $postRepository,
        protected NoteRepository $noteRepository,
        protected SluggerInterface $slugger,
        protected LoggerInterface $logger,
    ){}

    public function persistEntity(Post $post, AbstractPostCommand $command): Post
    {
        try {
            $post->setLanguage($command->language)
                ->setParent($command->getPostDto()->getPost()?->getParent())
                ->setType(new Type($command->type))
                ->setOptionType($command->optionType)
                ->setIcon($command->icon)
                ->setTitle($command->title)
                ->setDescription($command->description)
                ->setContent($command->content)
                ->setYoutubeId($command->youtubeId)
                ->setSlug($command->slug)
                ->setSource($command->source)
                ->setStatus(new Status($command->status))
                ->setCreatedBy($command->createdBy)
                ->setIsMain($command->isMain)
                ->setIsExclusive($command->isExclusive)
                ->setIsActual($command->isActual)
                ->setIsBreaking($command->isBreaking)
                ->setIsImportant($command->isImportant)
                ->setLinksNoIndex($command->linksNoIndex)
                ->setPublishedAt(new DateTime($command->publishedAt))
                ->setIsPublished(($post->getStatus()->isActive() && $post->getPublishedAt()->getTimestamp() <= time()))
                ->setMarkedWords($command->markedWords)
                ->setMeta(new Meta(...(array) $command->meta));

            $photoCommand = $command->photos;
            /** @var Photo|null $mainPhoto */
            $mainPhoto = $photoCommand->hasMain() ? $this->entityManager->getReference(Photo::class, $photoCommand->getMain()) : null;
            $post->setPhoto($mainPhoto);

            /** @var Category $category */
            $category = $this->entityManager->getReference(Category::class, $command->categoryId);
            $post->setCategory($category);

            /** @var User $author */
            $author = $this->entityManager->getReference(User::class, $command->authorId ?? $command->createdBy);
            $post->setAuthor($author);

            /** @var Person $expert */
            $expert = (!$command->expertId) ? null : $this->entityManager->getReference(Person::class, $command->expertId);
            $post->setExpert($expert);

            /** @var User $translator */
            $translator = (!$command->translatorId) ? null : $this->entityManager->getReference(User::class, $command->translatorId);
            $post->setTranslator($translator);

            /** @var City $city */
            $city = (!$command->cityId) ? null : $this->entityManager->getReference(City::class, $command->cityId);
            $post->setCity($city);

            /** @var Story|null $story */
            $story = !empty($command->storyId) ? $this->entityManager->getReference(Story::class, $command->storyId) : null;
            $post->setStory($story);

            $post->syncTags($this->prepareTags($command->tags, $command->language))
                ->sync('photoRelation', $this->preparePhotos($post, $photoCommand))
                ->sync('persons', $this->preparePersons($command->persons))
                ->sync('exports', $this->prepareExports($post, $command->exports))
                ->sync('relatedPosts', $this->prepareRelatedPosts($command->relatedPosts));

            $this->postRepository->save($post);
            $this->setParentIfNotDetermined($post);
            $this->syncNote($post, $command->note);
        } catch (ORMException | Exception $e) {
            $this->logger->error('Occurred error while persisting entity', ['exception' => $e]);
        }

        return $post;
    }

    private function prepareTags(array $tags, string $language): Collection
    {
        $collection = new ArrayCollection();

        foreach ($tags as $tagName) {
            $slug = $this->slugger->slug(mb_strtolower($tagName, 'UTF-8'));
            if (($tag = $this->entityManager->getRepository(Tag::class)->findOneBy(['slug' => $slug])) === null) {
                $tag = new Tag();
                $tag->setSlug($slug->toString());
            }

            $translation = $tag->getTranslations()
                ->filter(fn (TagTranslation $translation) => $translation->getLanguage() === $language)->first();

            if (empty($translation)) {
                $tag->addTranslation((new TagTranslation())
                    ->setName($tagName)
                    ->setLanguage($language)
                    ->setTag($tag)
                );
            }

            $collection->add($tag);
        }

        return $collection;
    }

    /**
     * @param array $persons
     * @return Collection
     * @throws ORMException
     */
    private function preparePersons(array $persons): Collection
    {
        $collection = new ArrayCollection();
        foreach ($persons as $personId)
            $collection->add($this->entityManager->getReference(Person::class, $personId));
        return $collection;
    }

    private function prepareExports(Post $post, array $exports): Collection
    {
        $collection = new ArrayCollection();
        foreach ($exports as $exportType) {
            $export = $this->entityManager->contains($post)
                ? $this->entityManager->getRepository(Export::class)
                    ->findOneBy(['post' => $post->getId(), 'type' => $exportType])
                : null;

            $collection->add($export ?? (new Export())->setType($exportType)->setPost($post));
        }

        return $collection;
    }

    private function preparePhotos(Post $post, PhotoCommand $photoCommand): Collection
    {
        $photoRelations = new ArrayCollection();

        foreach ($photoCommand->photos as $i => $photoId) {
            $photoRelation = $post->getPhotoRelation()
                ->filter(fn (PostPhoto $postPhoto) => $postPhoto->getPhoto()->getId() == $photoId)->first();

            if (!$photoRelation) {
                $photoRelation = (new PostPhoto())
                    ->setPhoto($this->entityManager->getReference(Photo::class, $photoId))
                    ->setPost($post);
            }

            $photoRelation->setSort($i + 1);
            $photoRelations->add($photoRelation);
        }

        return $photoRelations;
    }

    private function prepareRelatedPosts(array $relatedPosts): Collection
    {
        return (new ArrayCollection($relatedPosts))
            ->map(fn (int $id) => $this->entityManager->getReference(Post::class, $id));
    }

    private function syncNote(Post $post, ?string $note = null): void
    {
        if (!$post->getParent()) return;

        if (!($noteObj = $this->noteRepository->findOneBy(['postGroupId' => $post->getParent()->getId()]))) {
            if (empty($note)) return;
            $noteObj = new Note();
            $noteObj->setPostGroupId($post->getParent()->getId());
        } elseif (empty($note)) {
            $this->noteRepository->remove($noteObj);
            return;
        }

        $this->noteRepository->save($noteObj->setBody($note));
    }

    private function setParentIfNotDetermined(Post $post)
    {
        if ($post->getParent() !== null) return;
        $this->postRepository->save($post->setParent($post));
    }
}