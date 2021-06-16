<?php

namespace Ria\Bundle\PostBundle\Query\ViewModel;

use DateTime;
use Ria\Bundle\CoreBundle\Component\Pipeline\Pipeline;
use Ria\Bundle\CoreBundle\Component\Pipeline\PipelineInterface;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\PostBundle\Entity\Post\Status;
use Ria\Bundle\PostBundle\Entity\Post\Type;
use Ria\Bundle\CoreBundle\Query\ViewModel;
use Ria\Bundle\PostBundle\Pipe\AddNoindexToExternalLinks;
use Ria\Bundle\PostBundle\Pipe\ClearEmptyParagraphs;
use Ria\Bundle\PostBundle\Pipe\ClearUnnecessaryTokens;
use Ria\Bundle\PostBundle\Pipe\ExtractAmpTwitterWidgets;
use Ria\Bundle\PostBundle\Pipe\ExtractAmpVkWidgets;
use Ria\Bundle\PostBundle\Pipe\ExtractFacebookWidgets;
use Ria\Bundle\PostBundle\Pipe\ExtractInstagramWidgets;
use Ria\Bundle\PostBundle\Pipe\ExtractPhotos;
use Ria\Bundle\PostBundle\Pipe\ExtractQuotes;
use Ria\Bundle\PostBundle\Pipe\ExtractVotes;
use Ria\Bundle\PostBundle\Pipe\ExtractWidgets;
use Ria\Bundle\PostBundle\Pipe\ExtractYoutubeWidgets;
use Ria\Bundle\PostBundle\Pipe\RemoveIframesWithHttpSource;
use Ria\Bundle\PostBundle\Pipe\RemoveStyleAttributes;
use Ria\Bundle\PostBundle\Pipe\ReplaceIframeToAmpIframe;
use Ria\Bundle\PostBundle\Pipe\ReplaceImgToAmpImg;
use Ria\Bundle\PostBundle\Pipe\StylizeSimpleBlockquotes;
use Ria\Bundle\CoreBundle\Helper\StringHelper;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Translation\Translator;


/**
 * Class PostViewModel
 * @package Ria\Bundle\PostBundle\Query\ViewModel
 *
 * @property int $id
 * @property int $parent
 * @property int $category_id
 * @property string $title
 * @property string $description
 * @property string $slug
 * @property string|array $markedWords
 * @property string $type
 * @property string $status
 * @property string $icon
 * @property string $content
 * @property string $language
 * @property bool $isPublished
 * @property string $category_title
 * @property string $category_slug
 * @property DateTime $publishedAt
 * @property DateTime $createdAt
 * @property DateTime $updatedAt
 * @property string|null $image
 * @property int|null $photoId
 * @property string|null $youtubeId
 * @property bool $isMain
 * @property bool $isExclusive
 * @property bool $isActual
 * @property bool $isBreaking
 * @property bool $has_audio
 * @property bool $isImportant
 * @property bool $linksNoIndex
 * @property integer $views
 * @property string $author_name
 * @property string $authorFirstName
 * @property string $authorLastName
 * @property string $authorSlug
 * @property string|null $authorThumb
 * @property string|null $authorPosition
 * @property string $expert_first_name
 * @property string $expert_last_name
 * @property string $expert_slug
 * @property string|null $expert_thumb
 * @property string|null $expert_position
 * @property string|null $image_author
 * @property string|null $image_information
 * @property string|null $speech_filename
 * @property int $speech_duration
 * @property int $speech_filesize
 * @property string city_title
 * @property string city_slug
 * @property array $tags
 * @property Meta $meta
 */
class PostViewModel extends ViewModel
{

    public function isPhoto(): bool
    {
        return $this->type == Type::PHOTO;
    }

    public function getPreparedTitle(): string
    {
        $title = $this->getTitleWithType() . $this->getMarkedWords();

        if ($this->hasAudio()) {
            $title .= '<span class="icon"><i class="icon-volume-on"></i></span>';
        }

        return $title;
    }

    public function getTitleWithType(): string
    {
        return $this->getTypes() . $this->title;
    }

    public function getDescription(): string
    {
        if (empty($this->description)) {
            $this->description = StringHelper::clearDoubleQuotes(StringHelper::getFirstParagraph($this->content) ?? '');
        }
        return $this->description;
    }

    public function getTypes(): string
    {
        $tags = [];

        $translator = new Translator($this->language);

        if ($this->type == Type::PHOTO) {
            $tags[] = "<span class='type'>" . $translator->trans(Type::PHOTO, ['category' => 'common']) . "</span>";
        }

        if ($this->type == Type::VIDEO || $this->hasYoutubeVideo()) {
            $tags[] = "<span class='type'>" . $translator->trans(Type::VIDEO, ['category' => 'common']) . "</span>";
        }

        return implode('', $tags);
    }

    public function getMarkedWords(): string
    {
        $tags = [];
        if (!empty($this->markedWords)) {
            foreach ($this->markedWords as $word) {
                $tags[] = "<span class='official'>{$word}</span>";
            }
        }
        return implode('', $tags);
    }

    public function getTitleWithoutQuotes(): string
    {
        return str_replace('"', '', $this->title);
    }

    public function getTitleWithEllipsis() : string
    {
        return (mb_strlen($this->title, 'utf-8') > 88) ? mb_substr($this->title, 0, 88, 'utf-8') . '...' : $this->title;
    }

    public function getPreparedContent(PipelineInterface $pipeline): string
    {
        $pipes = [
            ExtractYoutubeWidgets::class,
            ExtractInstagramWidgets::class,
            ExtractFacebookWidgets::class,
            ExtractWidgets::class,
            ExtractPhotos::class,
            ExtractQuotes::class,
            ExtractVotes::class,
            StylizeSimpleBlockquotes::class
        ];

        if ($this->linksNoIndex) {
            $pipes[] = AddNoindexToExternalLinks::class;
        }

        $pipes[] = ClearEmptyParagraphs::class;
        $pipes[] = ClearUnnecessaryTokens::class;

        return $pipeline
            ->send($this->content)
            ->through($pipes)
            ->thenReturn();
    }

    /**
     * @param Pipeline $pipeline
     * @return string
     */
    public function getPreparedAmpContent(Pipeline $pipeline): string
    {
        /** @var ExtractYoutubeWidgets $extractYoutubeWidgets */
        $extractYoutubeWidgets = $pipeline->getContainer()->get(ExtractYoutubeWidgets::class);
        $extractYoutubeWidgets->setReplaceFormat('amp');

        /** @var ExtractFacebookWidgets $extractFacebookWidgets */
        $extractFacebookWidgets = $pipeline->getContainer()->get(ExtractFacebookWidgets::class);
        $extractFacebookWidgets->setReplaceFormat('amp');

        /** @var ExtractInstagramWidgets $extractInstagramWidgets */
        $extractInstagramWidgets = $pipeline->getContainer()->get(ExtractInstagramWidgets::class);
        $extractInstagramWidgets->setReplaceFormat('amp');

        $pipes = [
            $extractYoutubeWidgets,
            $extractFacebookWidgets,
            $extractInstagramWidgets,
            ExtractAmpTwitterWidgets::class,
            ExtractAmpVkWidgets::class,
            ExtractWidgets::class,
            ExtractPhotos::class,
            ExtractQuotes::class,
            new ReplaceImgToAmpImg(['width' => 610, 'height' => 486, 'layout' => 'responsive']),
            RemoveIframesWithHttpSource::class,
            new ReplaceIframeToAmpIframe([
                'width'       => 610,
                'height'      => 486,
                'layout'      => 'responsive',
                'frameborder' => 0,
                'sandbox'     => 'allow-scripts allow-same-origin'
            ]),
            RemoveStyleAttributes::class,
            ClearEmptyParagraphs::class,
            ClearUnnecessaryTokens::class
        ];

        return $pipeline
            ->send($this->content)
            ->through($pipes)
            ->thenReturn();
    }

    public function hasImage(): bool
    {
        return !empty($this->image);
    }

    public function hasYoutubeVideo(): bool
    {
        return !empty($this->youtubeId);
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function isActive(): bool
    {
        return in_array($this->status, Status::publishedOnes());
    }

    public function isOpinion(): bool
    {
        return $this->type === Type::OPINION;
    }

    public function isPrivate(): bool
    {
        return $this->status == Status::PRIVATE;
    }

    public function isFuture(): bool
    {
        return $this->publishedAt->getTimestamp() > time();
    }

    public function getAuthorName(): string
    {
        return $this->authorFirstName . ' ' . $this->authorLastName;
    }

    public function getExpertName(): string
    {
        return $this->expert_first_name . ' ' . $this->expert_last_name;
    }

    public function hasAudio(): bool
    {
        return isset($this->has_audio) ? (bool) $this->has_audio : false;
    }

}