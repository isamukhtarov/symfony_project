<?php

declare(strict_types=1);

namespace Ria\Bundle\CurrencyBundle\Console\Command;

use Generator;
use Exception;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use League\Tactician\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ria\Bundle\CurrencyBundle\Parsers\ParserInterface;
use Ria\Bundle\CurrencyBundle\Command\CreateCurrencyCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CurrencyParserConsoleCommand extends Command
{
    protected array $cacheKeys = [];

    public function __construct(
        private CommandBus $bus,
        private LoggerInterface $logger,
        private ContainerInterface $container,
        protected RedisClient $redisClient,
        protected ParameterBagInterface $parameterBag,
        string $name = null
    )
    {
        parent::__construct($name);

        $this->cacheKeys = $parameterBag->get('app.cache_keys');
    }

    protected function configure()
    {
        $this->addArgument(
            'parser', InputArgument::REQUIRED, 'This is parser name'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $parser = $input->getArgument('parser');
        $resources = [];

        try {
            foreach ($this->getParser($parser) as $resource)
                $resources[] = $resource;

            $this->bus->handle(new CreateCurrencyCommand($resources));
            $this->redisClient->del($this->cacheKeys['currency']);
        } catch (Exception $e) {
            $this->logger->error($e);
            $output->writeln($e->getMessage());
        }
        return Command::SUCCESS;
    }

    /**
     * @param string $parserName
     * @return Generator|array
     */
    public function getParser(string $parserName): Generator|array
    {
        if (!in_array($parserName, ['brent', 'cbar', 'nbg']))
            throw new InvalidArgumentException("Parser {$parserName} not found");

        /** @var ParserInterface $parser */
        $parser = $this->container->get("ria_currency.{$parserName}_parser");
        return $parser->parse();
    }
}