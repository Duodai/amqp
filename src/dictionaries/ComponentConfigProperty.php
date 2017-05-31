<?php


namespace duodai\amqp\dictionaries;

use duodai\amqp\common\Enum;

/**
 * Class ComponentConfigProperty
 * @author Michael Janus <mailto:abyssal@mail.ru>
 */
class ComponentConfigProperty extends Enum
{
    const SERVERS_LIST = 'servers';
    const HOST = 'host';
    const PORT = 'port';
    const LOGIN = 'login';
    const PASSWORD = 'password';
    const VIRTUAL_HOST = 'vhost';
    const READ_TIMEOUT = 'read_timeout';
    const WRITE_TIMEOUT = 'write_timeout';
    const CONNECT_TIMEOUT = 'connect_timeout';
    const MAX_TRIES_TO_CONNECT = 'maxTriesToConnect';
}
