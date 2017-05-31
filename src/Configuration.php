<?php


namespace duodai\amqp;

use duodai\amqp\dictionaries\ComponentConfigProperty;


/**
 * Class Configuration
 * Get configuration from component config format
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class Configuration
{ // TODO Remove old yii2 component based config system, make new object-oriented one

    /**
     *
     */
    const DEFAULT_MAX_TRIES_TO_CONNECT = 2;

    /**
     * @var array
     */
    protected $connectionConfigurations = [];
    /**
     * @var int
     */
    protected $maxTriesToConnect = self::DEFAULT_MAX_TRIES_TO_CONNECT;

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->connectionConfigurations = $this->mapConnectionSettings($settings);
        $this->maxTriesToConnect = $this->fetchMaxTriesToConnect($settings);
    }

    /**
     *
     * @param $settings
     * @return array
     * @throws AmqpException
     */
    protected function mapConnectionSettings($settings)
    {
        $servers = $this->fetchServersList($settings);
        $output = [];
        foreach ($servers as $server) {
            $output[] = [
                ComponentConfigProperty::HOST => $server,
                ComponentConfigProperty::PORT => $this->fetchPort($settings),
                ComponentConfigProperty::LOGIN => $this->fetchLogin($settings),
                ComponentConfigProperty::PASSWORD => $this->fetchPassword($settings),
                ComponentConfigProperty::VIRTUAL_HOST => $this->fetchVirtualHost($settings),
                ComponentConfigProperty::READ_TIMEOUT => $this->fetchReadTimeout($settings),
                ComponentConfigProperty::WRITE_TIMEOUT => $this->fetchWriteTimeout($settings),
                ComponentConfigProperty::CONNECT_TIMEOUT => $this->fetchConnectTimeout($settings),
            ];
        }
        return $output;
    }

    /**
     * @param array $settings
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function fetchServersList(array $settings)
    {
        if (!isset($settings[ComponentConfigProperty::SERVERS_LIST])) {
            throw new \InvalidArgumentException(
                __METHOD__ . ' error: servers are not defined. Check component configuration settings'
            );
        }
        $servers = $settings[ComponentConfigProperty::SERVERS_LIST];
        Assert::notEmpty($servers, __METHOD__ . ' error: servers list is empty');
        Assert::allString($servers, __METHOD__ . ' error: servers list mudt be an array of string values');
        return $servers;
    }

    /**
     * @param array $settings
     * @return int|null
     * @throws \InvalidArgumentException
     */
    protected function fetchPort(array $settings)
    {
        if (isset($settings[ComponentConfigProperty::PORT])) {
            $port = $settings[ComponentConfigProperty::PORT];
        } else {
            throw new \InvalidArgumentException(__METHOD__ . ' error: required param "port" is undefined');
        }
        Assert::integerish($port, __METHOD__ . ' error: port must be an integer, got ' . gettype($port));
        return (int)$port;
    }

    /**
     * @param array $settings
     * @return string|null
     * @throws \InvalidArgumentException
     */
    protected function fetchLogin(array $settings)
    {
        if (isset($settings[ComponentConfigProperty::LOGIN])) {
            $login = $settings[ComponentConfigProperty::LOGIN];
        } else {
            throw new \InvalidArgumentException(__METHOD__ . ' error: required param "login" is undefined');
        }
        Assert::string($login, __METHOD__ . ' error: login must be a string, got ' . gettype($login));
        return $login;
    }

    /**
     * @param array $settings
     * @return string|null
     * @throws \InvalidArgumentException
     */
    protected function fetchPassword(array $settings)
    {
        if (isset($settings[ComponentConfigProperty::PASSWORD])) {
            $password = $settings[ComponentConfigProperty::PASSWORD];
        } else {
            throw new \InvalidArgumentException(__METHOD__ . ' error: required param "password" is undefined');
        }
        Assert::string($password, __METHOD__ . ' error: password must be a string, got ' . gettype($password));
        return $password;
    }

    /**
     * @param array $settings
     * @return string|null
     * @throws \InvalidArgumentException
     */
    protected function fetchVirtualHost(array $settings)
    {
        if (isset($settings[ComponentConfigProperty::VIRTUAL_HOST])) {
            $virtualHost = $settings[ComponentConfigProperty::VIRTUAL_HOST];
        } else {
            $virtualHost = null;
        }
        Assert::nullOrString(
            $virtualHost,
            __METHOD__ . ' error: virtual_host must be a string or null, got ' . gettype($virtualHost)
        );
        return $virtualHost;
    }

    /**
     * @param array $settings
     * @return float|null
     * @throws \InvalidArgumentException
     */
    protected function fetchReadTimeout(array $settings)
    {
        if (isset($settings[ComponentConfigProperty::READ_TIMEOUT])) {
            $readTimeout = (float)$settings[ComponentConfigProperty::READ_TIMEOUT];
        } else {
            $readTimeout = null;
        }
        Assert::nullOrNumeric(
            $readTimeout,
            __METHOD__ . ' error: read_timeout must be a numeric value or null, got ' . gettype($readTimeout)
        );
        Assert::greaterThan(
            $readTimeout,
            0,
            __METHOD__ . ' error: read_timeout config setting must be a positive number'
        );
        return $readTimeout;
    }

    /**
     * @param array $settings
     * @return float|null
     * @throws \InvalidArgumentException
     */
    protected function fetchWriteTimeout(array $settings)
    {
        if (isset($settings[ComponentConfigProperty::WRITE_TIMEOUT])) {
            $writeTimeout = $settings[ComponentConfigProperty::WRITE_TIMEOUT];
        } else {
            $writeTimeout = null;
        }
        Assert::nullOrNumeric(
            $writeTimeout,
            __METHOD__ . ' error: write_timeout must be a numeric value or null, got ' . gettype($writeTimeout)
        );
        Assert::greaterThan(
            $writeTimeout,
            0,
            __METHOD__ . ' error: write_timeout config setting must be a positive number'
        );
        return $writeTimeout;
    }

    /**
     * @param array $settings
     * @return float|null
     * @throws \InvalidArgumentException
     */
    protected function fetchConnectTimeout(array $settings)
    {
        if (isset($settings[ComponentConfigProperty::CONNECT_TIMEOUT])) {
            $connectTimeout = $settings[ComponentConfigProperty::CONNECT_TIMEOUT];
        } else {
            $connectTimeout = null;
        }
        Assert::nullOrNumeric(
            $connectTimeout,
            __METHOD__ . ' error: connect_timeout must be a numeric value or null, got ' . gettype($connectTimeout)
        );
        Assert::greaterThan(
            $connectTimeout,
            0,
            __METHOD__ . ' error: connect_timeout config setting must be a positive number'
        );
        return $connectTimeout;
    }

    /**
     * @param array $settings
     * @return int
     * @throws \InvalidArgumentException
     */
    protected function fetchMaxTriesToConnect(array $settings)
    {
        if (isset($settings[ComponentConfigProperty::MAX_TRIES_TO_CONNECT])) {
            $maxTriesToConnect = $settings[ComponentConfigProperty::MAX_TRIES_TO_CONNECT];
        } else {
            $maxTriesToConnect = self::DEFAULT_MAX_TRIES_TO_CONNECT;
        }
        Assert::integer(
            $maxTriesToConnect,
            __METHOD__ . ' error: maxTriesToConnect config setting must be a positive integer'
        );
        // Ensure value is positive
        Assert::greaterThan(
            $maxTriesToConnect,
            0,
            __METHOD__ . ' error: maxTriesToConnect config setting must be a positive integer'
        );
        return $maxTriesToConnect;
    }

    /**
     * @return array
     */
    public function getConnectionConfigurations()
    {
        return $this->connectionConfigurations;
    }

    /**
     * @return int
     */
    public function getMaxTriesToConnect()
    {
        return $this->maxTriesToConnect;
    }
}
