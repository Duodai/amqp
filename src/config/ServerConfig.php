<?php


namespace Duodai\Amqp\config;


use Duodai\Amqp\exceptions\AmqpException;
use Webmozart\Assert\Assert;

class ServerConfig
{
    const HOST = 'host';
    const PORT = 'port';
    const LOGIN = 'login';
    const PASSWORD = 'password';
    const VIRTUAL_HOST = 'vhost';
    const READ_TIMEOUT = 'read_timeout';
    const WRITE_TIMEOUT = 'write_timeout';
    const CONNECT_TIMEOUT = 'connect_timeout';

    const DEFAULT_READ_TIMEOUT = 5;
    const DEFAULT_WRITE_TIMEOUT = 5;
    const DEFAULT_CONNECT_TIMEOUT = 10;
    const DEFAULT_CONNECT_TRIES_LIMIT = 5;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     * @throws AmqpException
     */
    public function __construct(array $config)
    {
        $required = $this->requiredParams();
        foreach ($required as $param) {
            if (empty($config[$param])) {
                throw new AmqpException("Amqp configuration error: Required param {$param} is not defined");
            }
        }
        $this->config = $config;
        if (isset($config[self::HOST])) {
            $this->validateHost($config[self::HOST]);
        }
        if (isset($config[self::PORT])) {
            $this->validatePort((int)$config[self::PORT]);
        }
        if (isset($config[self::LOGIN])) {
            $this->validateLogin($config[self::LOGIN]);
        }
        if (isset($config[self::PASSWORD])) {
            $this->validatePassword($config[self::PASSWORD]);
        }
        if (isset($config[self::VIRTUAL_HOST])) {
            $this->validateVirtualHost($config[self::VIRTUAL_HOST]);
        }
        if (isset($config[self::READ_TIMEOUT])) {
            $this->validateReadTimeout($config[self::READ_TIMEOUT]);
        }
        if (isset($config[self::WRITE_TIMEOUT])) {
            $this->validateWriteTimeout($config[self::WRITE_TIMEOUT]);
        }
        if (isset($config[self::CONNECT_TIMEOUT])) {
            $this->validateConnectTimeout($config[self::CONNECT_TIMEOUT]);
        }
    }

    /**
     * @return array
     */
    protected function requiredParams()
    {
        return [
            self::HOST,
            self::PORT
        ];
    }

    /**
     * @param string $host
     */
    protected function validateHost(string $host): void
    {
        Assert::minLength($host, 1, "Host can't be an empty string");
    }

    /**
     * @param int $port
     */
    protected function validatePort(int $port): void
    {
        Assert::greaterThan($port, 0, "Port must be greater than 0");
    }

    /**
     * @param string $login
     */
    protected function validateLogin(string $login): void
    {
        Assert::minLength($login, 1, "Login can't be an empty string");
    }

    /**
     * @param string $password
     */
    protected function validatePassword(string $password): void
    {
        // No additional validation required
    }

    /**
     * @param string $vhost
     */
    protected function validateVirtualHost(string $vhost): void
    {
        Assert::minLength($vhost, 1, "Virtual host can't be an empty string");
    }

    /**
     * @param int $timeout
     */
    protected function validateReadTimeout(int $timeout): void
    {
        Assert::greaterThan($timeout, 0, 'Read timeout must be greater than 0');
    }

    /**
     * @param int $timeout
     */
    protected function validateWriteTimeout(int $timeout): void
    {
        Assert::greaterThan($timeout, 0, 'Write timeout must be greater than 0');
    }

    /**
     * @param int $timeout
     */
    protected function validateConnectTimeout(int $timeout): void
    {
        Assert::greaterThan($timeout, 0, 'Connect timeout must be greater than 0');
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->getParam(self::HOST);
    }

    /**
     * @param string $param
     * @return mixed
     */
    protected function getParam(string $param)
    {
        $config = $this->config;
        if (!isset($config[$param])) {
            return null;
        }
        return $config[$param];
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->getParam(self::PORT);
    }

    /**
     * @return null|string
     */
    public function getLogin(): ?string
    {
        return $this->getParam(self::LOGIN);
    }

    /**
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->getParam(self::PASSWORD);
    }

    /**
     * @return null|string
     */
    public function getVirtualHost(): ?string
    {
        return $this->getParam(self::VIRTUAL_HOST);
    }

    /**
     * @return int
     */
    public function getReadTimeout(): int
    {
        return $this->getParam(self::READ_TIMEOUT) ?? self::DEFAULT_READ_TIMEOUT;
    }

    /**
     * @return int
     */
    public function getWriteTimeout(): int
    {
        return $this->getParam(self::WRITE_TIMEOUT) ?? self::DEFAULT_WRITE_TIMEOUT;
    }

    /**
     * @return int
     */
    public function getConnectTimeout(): int
    {
        return $this->getParam(self::CONNECT_TIMEOUT) ?? self::DEFAULT_CONNECT_TIMEOUT;
    }

    /**
     * @param int $tries
     */
    protected function validateConnectTriesLimit(int $tries): void
    {
        Assert::greaterThan($tries, 0, 'Connect tries limit must be greater than 0');
    }

}