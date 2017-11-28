<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Config for Rabbit MQ Library
 */
$config['rabbitmq'] = array(
    'host' => 'localhost',    // <- Your Host     (default: localhost)
    'port' => 5672,           // <- Your Port     (default: 5672)
    'user' => 'guest',     // <- Your User     (default: guest)
    'pass' => 'guest',     // <- Your Password (default: guest)
    'vhost' => '/'            // <- Your Vhost    (default: /)
);