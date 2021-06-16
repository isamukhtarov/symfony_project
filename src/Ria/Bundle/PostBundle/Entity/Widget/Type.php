<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Widget;

use InvalidArgumentException;

/**
 * Class Type
 * @package Ria\Bundle\PostBundle\Entity\Widget
 */
class Type
{
    public const YOUTUBE = 'youtube';
    public const FACEBOOK = 'facebook';
    public const TWITTER = 'twitter';
    public const INSTAGRAM = 'instagram';
    public const VKONTAKTE = 'vk';
    public const PLAYBUZZ = 'playbuzz';
    public const OTHER = 'other';

    /**
     * @var string
     */
    private string $type;

    public function __construct(string $type)
    {
        if (!in_array($type, self::all())) {
            throw new InvalidArgumentException('Invalid widget type: ' . $type);
        }

        $this->type = $type;
    }

    /**
     * @return array
     */
    public static function all(): array
    {
        return [
            self::YOUTUBE,
            self::FACEBOOK,
            self::TWITTER,
            self::INSTAGRAM,
            self::VKONTAKTE,
            self::PLAYBUZZ,
            self::OTHER
        ];
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isYouTube(): bool
    {
        return $this->type == self::YOUTUBE;
    }

    /**
     * @return bool
     */
    public function isFacebook(): bool
    {
        return $this->type == self::FACEBOOK;
    }

    /**
     * @return bool
     */
    public function isInstagram(): bool
    {
        return $this->type == self::INSTAGRAM;
    }

    /**
     * @return bool
     */
    public function isTwitter(): bool
    {
        return $this->type == self::TWITTER;
    }

    /**
     * @param string $content
     * @return static
     */
    public static function createFromContent(string $content): self
    {
        if (
            strpos($content, 'iframe') !== false
            && strpos($content, 'youtube') !== false
        ) {
            return new self(self::YOUTUBE);
        }
        if (
            strpos($content, '<blockquote class="twitter-tweet"') !== false
            || strpos($content, '<blockquote class="twitter-video"') !== false
        ) {
            return new self(self::TWITTER);
        }
        if (
            strpos($content, '<blockquote class="instagram-media"') !== false
            || strpos($content, '<blockquote data-instgrm-captioned data-instgrm-permalink') !== false
        ) {
            return new self(self::INSTAGRAM);
        }
        if (strpos($content, 'https://www.facebook.com/') !== false) {
            return new self(self::FACEBOOK);
        }
        if (strpos($content, '<div id="vk_post_') !== false) {
            return new self(self::VKONTAKTE);
        }
        if (
            strpos($content, 'playbuzz-bp-full-screen') !== false
            || strpos($content, 'class="playbuzz"') !== false
        ) {
            return new self(self::PLAYBUZZ);
        }

        return new self(self::OTHER);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->type;
    }
}