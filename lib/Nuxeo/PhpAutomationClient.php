<?php

/**
 * phpAutomationClient class
 *
 * Class which initializes the php client with an URL
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 */
class Nuxeo_PhpAutomationClient
{
  private $url;

  public function __construct($url = 'http://localhost:8080/nuxeo/site/automation'){
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
  public function getSession($username = 'Administrator', $password = 'Administrator'){
    $this->session = $username . ":" . $password;
    $session = new Nuxeo_Session($this->url, $this->session);
    return $session;
  }
}