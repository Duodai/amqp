<?php


namespace duodai\amqp;

use duodai\amqp\config\SettingsConfig;
use duodai\amqp\dictionaries\ComponentConfigProperty;
use Webmozart\Assert\Assert;


/**
 * Class Configuration
 * Get configuration from component config format
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class Configuration
{


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
