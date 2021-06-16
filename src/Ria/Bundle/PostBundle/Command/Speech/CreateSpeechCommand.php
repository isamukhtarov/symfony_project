<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Speech;

class CreateSpeechCommand extends SpeechCommand
{
    public int $postId;
}