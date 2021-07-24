<?php

declare(strict_types=1);

namespace MatchPlanner\Command;

use Exception;
use MatchPlanner\Components\MatchGenerator;
use MatchPlanner\Exceptions\FileNotFoundException;
use OutOfRangeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use UnexpectedValueException;

class GenerateMatches extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'hbb:generate:matches';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import excel with player names and receive excel with random matches')
            ->addArgument('filepath', InputArgument::REQUIRED)
            ->addOption(
                'courtsCount',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Specify the amount of courts to limit the amount of matches to be created',
                10
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            [$maxCourts, $filepath] = $this->validateInput($input);

            $matchGenerator = new MatchGenerator($maxCourts);
            $matchGenerator->generateMatches($filepath);
        } catch (Exception $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @return array
     * @throws \MatchPlanner\Exceptions\FileNotFoundException
     */
    private function validateInput(InputInterface $input): array
    {
        $filepath = $input->getArgument('filepath');

        if ($filepath === null) {
            throw new UnexpectedValueException('Missing filepath');
        }

        if (!file_exists(__DIR__ . '/../../files/' . $filepath)) {
            throw new FileNotFoundException(sprintf('Could not find file at %s', $filepath));
        }

        $courtsCount = (int)$input->getOption('courtsCount');

        if ($courtsCount < 0 || $courtsCount > 100) {
            throw new OutOfRangeException('courtsCount option should be greater than 0 and smaller than 100 courts');
        }

        return [$courtsCount, $filepath];
    }
}