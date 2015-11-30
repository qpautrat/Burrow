<?php
namespace Burrow\Swarrot;

use Burrow\QueueHandler;
use Burrow\QueueConsumer;
use Burrow\Daemonizable;
use Burrow\Swarrot\Processor\QueueConsumerProcessor;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Swarrot\Broker\MessageProvider\MessageProviderInterface;
use Swarrot\Consumer;
use Swarrot\Processor\Stack\Builder;

class SwarrotAsyncHandler implements QueueHandler, Daemonizable, LoggerAwareInterface
{
    /**
     * @var QueueConsumer
     */
    protected $consumer;

    /**
     * @var MessageProviderInterface
     */
    protected $messageProvider;

    /**
     * @var Consumer
     */
    protected $daemon;

    /**
     * @var Builder
     */
    protected $stack;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     *
     * @param MessageProviderInterface $messageProvider
     */
    public function __construct(MessageProviderInterface $messageProvider)
    {
        $this->messageProvider = $messageProvider;
    }

    /**
     * Sets the consumer
     *
     * @param  QueueConsumer $consumer
     * @return void
     */
    public function registerConsumer(QueueConsumer $consumer)
    {
        $this->consumer = $consumer;
    }

    /**
     * Run as a daemon
     *
     * @return void
     */
    public function daemonize()
    {
        if ($this->logger) {
            $this->logger->info('Starting AMqpAsyncHandler daemon...');
        }

        $processor = (new Builder())
            ->push('Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor')
            ->push('Swarrot\Processor\Ack\AckProcessor', $this->messageProvider)
            ->resolve(new QueueConsumerProcessor($this->consumer));

        $daemon = new Consumer($this->messageProvider, $processor);
        $daemon->consume();
    }

    /**
     * Stop current connection / daemon
     *
     * @return void
     */
    public function shutdown()
    {
        if ($this->logger) {
            $this->logger->info('Closing AMqpAsyncHandler daemon...');
        }

        // TODO close something?
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}