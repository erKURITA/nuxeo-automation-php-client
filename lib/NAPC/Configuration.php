<?php

namespace NAPC;

/**
 * Class Configuration
 *
 * This class will handle all configuration-related variables
 *
 * @author  Alejandro Carrillo <j.alec.n@gmail.com>
 *
 * @package NAPC
 *
 * @method getPort
 * @method getHost
 * @method getIsHttpsSecure
 * @method getUsername
 * @method getPassword
 * @method getAutomationPath
 * @method getTempPath
 *
 * @method setPort
 * @method setHost
 * @method setIsHttpsSecure
 * @method setUsername
 * @method setPassword
 * @method setAutomationPath
 * @method setTempPath
 */
class Configuration
{
    private $config = array(
        'host'            => 'localhost',
        'port'            => '8080',
        'is_https_secure' => false,
        'username'        => 'Administrator',
        'password'        => 'Administrator',
        'automation_path' => '/nuxeo/site/automation',
        'temp_path'       => 'blobs'
    );
    private static $instance = null;

    /**
     * Class constructor
     *
     * @param array $configuration
     */
    private function __construct(array $configuration = array())
    {
        $this->updateConfiguration($configuration);
        $this->config['user_handle'] = $this->getUserHandle();
    }

    /**
     *
     * Construct and return the connection URL, such as
     *
     * http://Administrator:Administrator@localhost:8080/nuxeo/site/automation
     *
     * @param bool $withUserHandle
     *
     * @return string
     */
    public function getURL($withUserHandle = true)
    {
        $protocol = ($this->isSecure()) ? 'https://' : 'http://';

        $userHandle = ($withUserHandle === true) ? $this->getUserHandle() : '';
        if ($userHandle != '') {
            $userHandle .= '@';
        }

        $fullhost = array($this->getHost());
        $port     = $this->getPort();

        if ($port != '') {
            $fullhost[] = $port;
        }

        $host = implode(':', $fullhost);

        $automationPath = $this->getAutomationPath();

        $url = $protocol.$userHandle.$host.$automationPath;

        return $url;
    }

    /**
     * @return string
     */
    public function getUserHandle()
    {
        return implode(':', array($this->getUsername(), $this->getPassword()));
    }

    /**
     * Returns whether to establish a secure connection (HTTPS) or not (HTTP)
     *
     * @return bool
     */
    public function isSecure()
    {
        return $this->getProperty('is_https_secure') === true;
    }

    /**
     * Magic method to access and set properties
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed|null
     */
    public function __call($name, array $arguments)
    {
        if (strpos($name, 'get') === 0) {
            $name = implode('', explode('get', $name));

            return $this->getProperty($name);
        } elseif (strpos($name, 'get') === 0) {
            $name = implode('', explode('set', $name));

            return $this->setProperty($name, $arguments[0]);
        }

        return $this->error('__call', $name);
    }

    /**
     * Sets a property with a given value. Triggers an error if the property does not exist.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed|null
     */
    private function setProperty($name, $value)
    {
        $propertyName = self::deCamelize($name);

        if (array_key_exists($propertyName, $this->config)) {
            return $this->config[$propertyName] = $value;
        }

        return $this->error('setProperty', $propertyName);
    }

    /**
     * Returns a configuration property, otherwise triggers an error
     *
     * @param   string $name
     *
     * @return  mixed   Configuration value
     */
    private function getProperty($name)
    {
        $propertyName = self::deCamelize($name);

        if (array_key_exists($propertyName, $this->config)) {
            return $this->config[$propertyName];
        }

        return $this->error('getProperty', $propertyName);
    }

    /**
     *
     * Prints a backtrace with the invoking method and the method called.
     *
     * @param        $methodName
     * @param string $method
     *
     * @return null
     */
    private function error($method, $methodName)
    {
        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via '.$method.'(): '.$methodName.
            ' in '.$trace[0]['file'].
            ' on line '.$trace[0]['line'],
            E_USER_ERROR
        );

        return null;
    }

    /**
     *
     * Singleton instantiation which calls Configuration::getInstance()
     *
     * @param array $configuration Configuration for this class
     *
     * @return \NAPC\Configuration|null
     */
    public static function createInstance(array $configuration = array())
    {
        return self::getInstance($configuration);
    }

    /**
     *
     * Singleton instantiation
     *
     * @param array $configuration Configuration for this class
     *
     * @return \NAPC\Configuration|null
     */
    public static function getInstance(array $configuration = array())
    {
        if (is_null(self::$instance)) {
            self::$instance = new Configuration($configuration);
        }

        return self::$instance;
    }

    /**
     * @param array $configuration
     */
    public function updateConfiguration(array $configuration)
    {
        foreach (array_keys($this->config) as $key) {
            $this->config[$key] = (isset($configuration[$key]) && $configuration[$key] != '')
                ? $configuration[$key]
                : $this->config[$key];
        }
    }

    /**
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
     *
     * @return string
     */
    public static function deCamelize($name)
    {
        //'exampleStringLikeThisONEHere' -> 'example String Like This ONEHere'
        $name = preg_replace('/(?!^)[[:upper:]]+/', ' $0', $name);
        //'example String Like This ONEHere' -> 'example String Like This ONE Here'
        $name = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', ' $0', $name);
        //'example String Like This ONE Here' -> array('example', 'String', 'Like', 'This', 'ONE', 'Here'
        $name = implode('_', array_filter(explode(' ', strtolower($name))));

        return $name;
    }

    /**
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
     *
     * @return string
     */
    public static function camelize($name)
    {
        $camelized = array_reduce(
            explode('_', strtolower($name)),
            function ($elementa, $elementb) {
                return ucfirst(trim($elementa)).''.ucfirst(trim($elementb));
            }
        );

        $camelized = lcfirst($camelized);

        return $camelized;
    }
}
