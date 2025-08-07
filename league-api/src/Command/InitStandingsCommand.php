<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\TeamStandingInitializer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:init-standings',
)]
class InitStandingsCommand extends Command
{
    public function __construct(private readonly TeamStandingInitializer $initializer)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initializer->initialize();
        $output->writeln('<info>Standings initialized.</info>');
        return Command::SUCCESS;
    }
}
