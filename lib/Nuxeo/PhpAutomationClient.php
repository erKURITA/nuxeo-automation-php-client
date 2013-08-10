<?php
namespace Nuxeo;

/**
 * phpAutomationClient class
 *
 * Class which initializes the php client with an URL
 *
 * @author Arthur GALLOUIN for NUXEO <agallouin@nuxeo.com>
 * @namespace Nuxeo
 *
 */
class PhpAutomationClient
{
    private $url;
    private $session = '';

    /**
     * @param string $url
     */
    public function __construct($url = 'http://localhost:8080/nuxeo/site/automation')
    {
        $this->url = $url;
    }

    /**
     * Open a session from a phpAutomationClient
     *
     * @author Arthur GALLOUIN for NUXEO <agallouin@nuxeo.com>
     *
     * @param string $username
     * @param string $password
     * @return \Nuxeo\Session\Session
     */
    public function getSession($username = 'Administrator', $password = 'Administrator')
    {
        $usernameHandle = $username . ":" . $password;
        $this->session = new Session\Session($this->url, $usernameHandle);
        return $this->session;
    }
}
