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

set_include_path(getcwd().'../lib/' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader/Autoloader.php';
// instantiate the loader
$loader = Zend_Loader_Autoloader::getInstance();

// specify class namespaces you want to be auto-loaded.
// 'Zend_' and 'ZendX_' are included by default
$loader->registerNamespace('Nuxeo_');

// optional argument if you want the auto-loader to load ALL namespaces
$loader->setFallbackAutoloader(true);

Configuration::createInstance();