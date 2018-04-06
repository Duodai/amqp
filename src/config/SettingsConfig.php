<?php


namespace duodai\amqp\config;


class SettingsConfig
{

    const CONNECT_TRIES_LIMIT = 'connectTriesLimit';
    const CONNECT_TRIES_INTERVAL = 'connectTriesInterval';
    const IS_CONNECT_TRIES_INTERVAL_INCREMENTAL = 'isConnectIntervalIncremental';
    const CONNECT_INTERVAL_INCREMENT = 'connectIncrement';
    const SHUFFLE_CONNECTIONS_ORDER = 'shuffle';

    /**
     * @var int
     */
    protected $connectTriesLimit;
    /**
     * @var int
     */
    protected $connectTriesInterval;
    /**
     * @var bool
     */
    protected $isConnectIntervalIncremental;
    /**
     * @var int
     */
    protected $connectIncrement;
    /**
     * @var bool
     */
    protected $shuffleConnections;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $config = array_merge($this->defaults(), $config);
        $this->connectTriesLimit = $config[self::CONNECT_TRIES_LIMIT];
        $this->connectTriesInterval = $config[self::CONNECT_TRIES_INTERVAL];
        $this->isConnectIntervalIncremental = $config[self::IS_CONNECT_TRIES_INTERVAL_INCREMENTAL];
        $this->connectIncrement = $config[self::CONNECT_INTERVAL_INCREMENT];
        $this->shuffleConnections = $config[self::SHUFFLE_CONNECTIONS_ORDER];
    }

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            self::CONNECT_TRIES_LIMIT => 5,
            self::CONNECT_TRIES_INTERVAL => 1000000,
            self::IS_CONNECT_TRIES_INTERVAL_INCREMENTAL => true,
            self::CONNECT_INTERVAL_INCREMENT => 1000000,
            self::SHUFFLE_CONNECTIONS_ORDER => true
        ];
    }

    /**
     * @return int
     */
    public function getConnectTriesLimit(): int
    {
        return $this->connectTriesLimit;
    }

    /**
     * @return int
     */
    public function getConnectTriesInterval(): int
    {
        return $this->connectTriesInterval;
    }

    /**
     * @return bool
     */
    public function isConnectIntervalIncremental(): bool
    {
        return $this->isConnectIntervalIncremental;
    }

    /**
     * @return int
     */
    public function getConnectIncrement(): int
    {
        return $this->connectIncrement;
    }

    /**
     * @return bool
     */
    public function isShuffleConnections(): bool
    {
        return $this->shuffleConnections;
    }
}