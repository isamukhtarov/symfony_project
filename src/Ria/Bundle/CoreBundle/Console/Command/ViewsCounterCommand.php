<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Console\Command;

use Ria\Bundle\CoreBundle\Component\ViewsCounter\Collector\BaseCollector;
use Ria\Bundle\CoreBundle\Component\ViewsCounter\PostsViewsProcessorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

/**
 * Class ViewsCounterCommand
 * @package Ria\Bundle\CoreBundle\Console\Command
 */
class ViewsCounterCommand extends Command
{

    public function __construct(
        protected BaseCollector $baseCollector,
        protected PostsViewsProcessorService $counterProcessor,
        protected ClientInterface $redisClient,
        string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addArgument(
            'fakeData', InputArgument::OPTIONAL, 'Whether use fake data or not', false
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $useFakeData = $input->hasArgument('fakeData') ? (bool)$input->getArgument('fakeData') : false;

        $this->baseCollector->useFakeData($useFakeData);

        $this->counterProcessor->addCollector($this->baseCollector);
        $this->counterProcessor->process();

        return self::SUCCESS;
    }

}