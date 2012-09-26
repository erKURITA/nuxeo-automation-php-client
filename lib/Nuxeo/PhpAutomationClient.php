<?php
namespace Nuxeo;

/**
 * phpAutomationClient class
 *
 * Class which initializes the php client with an URL
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 * @namespace Nuxeo
 *
 */
class PhpAutomationClient
{
  private $url;
  private $session = '';

  public function __construct($url = 'http://localhost:8080/nuxeo/site/automation')
  {
    $this->url = $url;
  }

  /**
   * getSession function
   *
   * Open a session from a phpAutomationClient
   *
   * @var        $username : username for your session
   *             $password : password matching the usename
   * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
   */
  public function getSession($username = 'Administrator', $password = 'Administrator')
  {
    $username_handle = $username . ":" . $password;
    $this->session = new Session\Session($this->url, $username_handle);
    return $this->session;
  }
}