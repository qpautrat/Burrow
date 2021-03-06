<?php

namespace Burrow\CLI;

use Burrow\Driver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class BindCommand extends Command
{
    /** @var Driver */
    private $driver;

    /**
     * DeclareQueueCommand constructor.
     *
     * @param Driver $driver
     *
     * @throws LogicException
     */
    public function __construct(Driver $driver)
    {
        parent::__construct();

        $this->driver = $driver;
    }

    protected function configure()
    {
        $this->setName('admin:bind')
            ->setDescription('Declares an exchange in RabbitMQ.')
            ->addArgument(
                'exchange',
                InputArgument::REQUIRED,
                'The name of the exchange to bind.'
            )
            ->addArgument(
                'queue',
                InputArgument::REQUIRED,
                'The name of the queue to bind.'
            )
            ->addOption(
                'routingKey',
                'k',
                InputOption::VALUE_REQUIRED,
                'The routing key to use.'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exchange = $input->getArgument('exchange');
        $queue = $input->getArgument('queue');
        $routingKey = ($input->getOption('routingKey') !== null) ? $input->getOption('routingKey') : '';

        $this->driver->bindQueue(
            $input->getArgument('exchange'),
            $input->getArgument('queue'),
            $routingKey
        );

        $output->writeln(sprintf(
            '<info>Bind exchange <comment>%s</comment> to queue <comment>%s</comment> [<comment>%s</comment>]</info>',
            $exchange,
            $queue,
            $routingKey
        ));
    }
}
