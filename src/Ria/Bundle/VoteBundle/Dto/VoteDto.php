<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Dto;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VoteDto
{
    public function __construct(
        private ?string $locale,
        private array $requestOptions
    ){}


    public function getLocale(): ?string
    {
        if (empty($this->locale)) {
            throw new NotFoundHttpException('Missing required parameters: lang');
        }

        return $this->locale;
    }

    public function getRequestOptions(): array
    {
        return $this->requestOptions;
    }
}