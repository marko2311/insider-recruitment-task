<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\SeasonScheduler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'league:generate-season',
)]
class GenerateSeasonCommand extends Command
{
    public function __construct(
        private readonly SeasonScheduler $seasonScheduler
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->seasonScheduler->generateSchedule();
            $output->writeln('<info>Schedule generated.</info>');
        } catch (\Throwable $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
