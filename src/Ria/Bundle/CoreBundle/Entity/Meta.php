<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Meta
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private string $meta;

    private array $decoded = [];

    public function __construct(
      private ?string $title = '',
      private ?string $description = '',
      private ?string $keywords = '',
    ) {
        $this->meta = json_encode(compact('title', 'description', 'keywords'));
    }

    public function getTitle(): string
    {
        return $this->getDecodedValue('title');
    }

    public function getDescription(): string
    {
        return $this->getDecodedValue('description');
    }

    public function getKeywords(): string
    {
        return $this->getDecodedValue('keywords');
    }

    public function getDecodedValue(string $value): string
    {
        $this->decodeMeta();

        return $this->decoded[$value] ?? '';
    }

    public function toArray(): array
    {
        $this->decodeMeta();

        return $this->decoded;
    }

    private function decodeMeta(): void
    {
        if (!$this->decoded)
            $this->decoded = json_decode($this->meta ?? '{}', true);
    }
}