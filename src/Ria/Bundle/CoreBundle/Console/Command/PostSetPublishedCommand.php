<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Console\Command;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Entity\Post\Status;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PostSetPublishedCommand
 * @package Ria\Bundle\CoreBundle\Console\Command
 */
class PostSetPublishedCommand extends Command
{

    public function __construct(
        protected EntityManagerInterface $entityManager,
        string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager->getConnection()->executeQuery(
            "UPDATE posts 
                 SET is_published = 1
                 WHERE status IN (:statuses) AND published_at < NOW() and is_published = 0",
            [
                'statuses' => implode("','", Status::publishedOnes()),
            ]
        );

        return self::SUCCESS;
    }

}