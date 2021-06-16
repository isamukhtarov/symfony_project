<?php

declare(strict_types=1);

namespace Ria\Bundle\WeatherBundle\Console\Command;

use Ria\Bundle\WeatherBundle\Components\RegionList;
use Ria\Bundle\WeatherBundle\Components\WeatherReceiverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WeatherReceiverConsoleCommand extends Command
{
    public function __construct(
        private WeatherReceiverInterface $receiver,
        string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->receiver->receive();
        return Command::SUCCESS;
    }
}