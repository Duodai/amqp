<?php

use duodai\amqp\config\Config;
use duodai\amqp\config\ExchangeConfig;
use duodai\amqp\config\QueueConfig;
use duodai\amqp\config\RouteConfig;
use duodai\amqp\config\ServerConfig;
use duodai\amqp\dictionaries\ExchangeFlag;
use duodai\amqp\dictionaries\ExchangeType;
use duodai\amqp\dictionaries\QueueFlag;

return [
    Config::SERVERS_OPTION => [
        'server1' => [
            ServerConfig::HOST => 'rabbit.example.com',
            ServerConfig::PORT => 5673,
            ServerConfig::LOGIN => 'user',
            ServerConfig::PASSWORD => 'pwd'
        ],

    ],
    Config::EXCHANGES_OPTION => [
        'default' => [
            ExchangeConfig::TYPE => ExchangeType::TYPE_DIRECT,
            ExchangeConfig::FLAGS => [
                ExchangeFlag::FLAG_DURABLE
            ]
        ],
    ],
    Config::QUEUES_OPTION => [
        'default' => [
            QueueConfig::FLAGS => [
                QueueFlag::FLAG_DURABLE,
                QueueFlag::FLAG_AUTODELETE
            ],
        ],
    ],
    Config::ROUTES_OPTION => [
        'default_default' => [
            RouteConfig::SOURCE_EXCHANGES => [
                'default',
            ],
            RouteConfig::DESTINATION_QUEUES => [
                'default'
            ],
        ],
    ],

];