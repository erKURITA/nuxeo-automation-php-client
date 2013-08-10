<?php
/**
 * Configuration can be done this way

$default_configuration = array(
'host'            => 'localhost',
'port'            => '8080',
'is_https_secure' => false,
'username'        => 'Administrator',
'password'        => 'Administrator',
'automation_path' => '/nuxeo/site/automation',
'temp_path'       => 'blobs'
);

 */

use Zend\Loader\StandardAutoloader as Autoloader;

require_once __DIR__ . '/lib/Zend/Loader/StandardAutoloader.php';

set_include_path(__DIR__ . '/lib' . PATH_SEPARATOR . get_include_path());


$autoloader_options = array(
    'namespaces' => array(
        'Zend'  => __DIR__ . '/lib/Zend',
        'Nuxeo' => __DIR__ . '/lib/Nuxeo',
        'NAPC'  => __DIR__ . '/lib/NAPC'
    )
);

// instantiate the loader
$loader = new Autoloader($autoloader_options);

$loader->register();

NAPC\Configuration::createInstance($default_configuration);
