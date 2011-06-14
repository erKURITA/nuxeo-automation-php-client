<?php

/**
 *
 * This class will handle all configuration-related variables
 * @author erkurita
 *
 */
class Configuration {
  private $_config = array(
      'host'            => 'localhost',
      'port'            => '8080',
      'is_https_secure' => false,
      'username'        => 'Administrator',
      'password'        => 'Administrator',
      'automation_path' => '/nuxeo/site/automation'
    );
  private static $_instance = null;


  /**
   *
   * Class constructor
   *
   * @param array $configuration
   */
  private function __construct(array $configuration = array())
  {
    foreach(array_keys($this->_config) as $key)
      $this->_config[$key] = (isset($configuration[$key]) && $configuration[$key] != '') ? $configuration[$key] : $this->_config[$key];

    $this->_config['user_handle'] = $this->getUserHandle();
  }

  /**
   *
   * Construct and return the connection URL, such as
   *
   * http://Administrator:Administrator@localhost:8080/nuxeo/site/automation
   */
  public function getURL($with_user_handle = true)
  {
    $protocol         = ($this->isSecure()) ? 'https://' : 'http://';

    $user_handle      = ($with_user_handle === true) ? $this->getUserHandle() : '';
    if ($user_handle != '')
      $user_handle   .= '@';

    $fullhost         = array($this->getHost());
    $port = $this->getPort();
    if ($port != '')
      $fullhost[]     = $port;

    $host             = implode(':',$fullhost);

    $automation_path  = $this->getAutomationPath();

    $url = $protocol.$user_handle.$host.$automation_path;

    return $url;
  }

  public function getUserHandle()
  {
    return implode(':', array($this->getUsername(),$this->getPassword()));
  }

  /**
   *
   * Returns whether to establish a secure connection (HTTPS) or not (HTTP)
   */
  public function isSecure()
  {
    return $this->_getProperty('is_https_secure') === true;
  }

  /**
   *
   * Magic method to access and set properties
   * @param string $name
   * @param array $arguments
   */
  public function __call($name, array $arguments)
  {
    if (strpos($name,'get') === 0)
    {
      $name = implode('',explode('get', $name));
      return $this->_getProperty($name);
    }
    else if (strpos($name,'get') === 0)
    {
      $name = implode('',explode('set', $name));
      return $this->_setProperty($name,$arguments[0]);
    }

    return $this->_error('__call',$name);

  }

  /**
   *
   * Sets a property with a given value. Triggers an error if the property does not exist.
   * @param unknown_type $name
   * @param unknown_type $value
   */
  private function _setProperty($name,$value)
  {
    $property_name = self::deCamelize($name);

    if (array_key_exists($property_name, $this->_config))
      return $this->_config[$property_name] = $value;

    return $this->_error('_setProperty', $property_name);
  }

  /**
   *
   * Returns a configuration property, otherwise triggers an error
   * @param   string  $name
   * @return  mixed   configuration value
   */
  private function _getProperty($name)
  {
    $property_name = self::deCamelize($name);

    if (array_key_exists($property_name, $this->_config))
      return $this->_config[$property_name];

    return $this->_error('_getProperty', $property_name);

  }

  /**
   *
   * Prints a backtrace with the invoking method and the method called.
   * @param string $method
   * @param string $method_name
   */
  private function _error($method,$method_name)
  {
    $trace = debug_backtrace();
    trigger_error(
        'Undefined property via '.$method.'(): ' . $method_name .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR);
    return null;
  }

  /**
   *
   * Singleton instantiation which calls Configuration::getInstance()
   * @param array $configuration Configuration for this class
   */
  static public function createInstance(array $configuration = array())
  {
    return self::getInstance($configuration);
  }

  /**
   *
   * Singleton instantiation
   * @param array $configuration Configuration for this class
   */
  static public function getInstance(array $configuration = array())
  {
    if (is_null(self::$_instance)){
      self::$_instance = new Configuration($configuration);
    }

    return self::$_instance;
  }

  /**
   *
   * De-constructs a camel-cased string.
   *
   * This function works as described below:
   *
   * - Let $name be a camelized string.
   * - Separate all the upper-case words apart.
   * - Separate the upper-case words from the capitalized words, since they weren't split by the first regex.
   * - Once separated, split the lower-case resulting space-separated string into an array.
   * - Filter the extra spaces out and join them together with a underscore again.
   * - The resulting string is returned.
   *
   * @param string $name
   */
  static public function deCamelize($name)
  {
    //'exampleStringLikeThisONEHere' -> 'example String Like This ONEHere'
    $name = preg_replace('/(?!^)[[:upper:]]+/', ' $0', $name);
    //'example String Like This ONEHere' -> 'example String Like This ONE Here'
    $name = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', ' $0', $name);
    //'example String Like This ONE Here' -> array('example', 'String', 'Like', 'This', 'ONE', 'Here'
    $name = implode('_',array_filter(explode(' ',strtolower($name))));

    return $name;
  }

  /**
   *
   * Creates a camel-cased representation on a string
   *
   * This function works as described below:
   *
   * - Let $name be a word-compounded, underscore-delimited string.
   * - Separate the string with explode() using underscore as the delimiter.
   * - Use array_reduce() to join the words back together, trimming and capitalizing each word.
   * - Lower the case of the first character of the resulting string.
   * - The resulting string is returned.
   *
   * @param string $name
   */
  static public function camelize($name)
  {
    $camelized = array_reduce(
              explode('_', strtolower($name)),
              function($elementa, $elementb){
                return ucfirst(trim($elementa)).''.ucfirst(trim($elementb));
              }
             );

    $camelized = lcfirst($camelized);

    return $camelized;
  }
}
