<?php
namespace Ria\Bundle\CoreBundle\Console\Command;

use Elasticsearch\Client;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Query\PostIndexer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateElasticPostsCommand extends Command
{
    public function __construct(
        protected Client $client,
        private PostRepository $postRepository,
        private PostIndexer $postIndexer,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '512M');

//        $io = new SymfonyStyle($input, $output);

        $lastItemTimeFile = __DIR__ . "/last_elastic_item_time.txt";
        $runFromBeginning = !is_file($lastItemTimeFile);
        $dateFrom = $runFromBeginning ? '2019-05-07 00:00:00' : file_get_contents($lastItemTimeFile);

        if ($runFromBeginning)
            $this->postIndexer->clear();

        /** @var Post[] $posts */
        $posts = $this->postRepository->getElasticPosts(5000, $dateFrom);

        foreach ($posts as $post) {
            /** @var Post @post */
            $this->postIndexer->index($post);
            file_put_contents($lastItemTimeFile, $post->getPublishedAt()->format('Y-m-d H:i:s'));
        }

//        $io->success("All posts migrated to ElasticSearch\n");
        return Command::SUCCESS;
    }
}
