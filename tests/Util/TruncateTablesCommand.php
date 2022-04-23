<?php
declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Tests\Util;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

final class TruncateTablesCommand extends Command
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        parent::__construct('database:truncate');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('truncate tables')
            ->setHelp('truncate all tables')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (true === $input->isInteractive()) {
            if (true === $this->ask($input, $output)) {
                $output->write('Truncating database... ');
            } else {
                $output->writeln('Truncate was not performed.');
                return Command::SUCCESS;
            }
        }

        $tables = $this->connection->createSchemaManager()->listTables();

        foreach ($tables as $theTable) {
            $this->connection->executeQuery('TRUNCATE ' . $theTable->getName() . ' CASCADE');
        }

        $output->writeln('Done.');

        return Command::SUCCESS;
    }

    private function ask(InputInterface $input, OutputInterface $output): bool
    {
        $helper = $this->getHelper('question');
        $output->writeln('Make sure you have the environment variables set correctly in ".env" file before this.');
        $question = new ConfirmationQuestion('Continue with this action? (yes/no)', false);
        /** @phpstan-ignore-next-line */
        return $helper->ask($input, $output, $question);
    }
}
