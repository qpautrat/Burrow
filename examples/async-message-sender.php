#!/usr/bin/php
<?php

if (!isset($argv[1])) {
    $io = fopen('php://stderr', 'w+');
    fwrite($io, "usage: php async-message-sender-v2.php <nbevents:int>\n");
    die;
}

require_once __DIR__ . '/../vendor/autoload.php';

$host = '127.0.0.1';
$port = 5672;
$user = 'guest';
$pass = 'guest';
$exchange = 'xchange';

//$publisher = new \Burrow\RabbitMQ\AmqpAsyncPublisher($host, $port, $user, $pass, $exchange);

// $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection($host, $port, $user, $pass);
// $channel = $connection->channel();
// $messagePublisher = new \Swarrot\Broker\MessagePublisher\PhpAmqpLibMessagePublisher($channel, $exchange);

$credentials = array('host' => $host, 'port' => $port, 'vhost' => '/', 'login' => $user, 'password' => $pass);
$connection = new \AMQPConnection($credentials);
$connection->connect();
$channel = new \AMQPChannel($connection);
$xchange = new \AMQPExchange($channel); $xchange->setName($exchange);
$messagePublisher = new \Swarrot\Broker\MessagePublisher\PeclPackageMessagePublisher($xchange);

$publisher = new \Burrow\Swarrot\SwarrotAsyncPublisher($messagePublisher);

for ($i = 0; $i < $argv[1]; ++$i) {
    $publisher->publish('event #'.$i);
}
