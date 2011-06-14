<?php

/**
 * Session class
 *
 * Class which stocks username,password, and open requests
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 */
class Nuxeo_Session
{

  private $urlLoggedIn;
  private $headers;
  private $requestContent;

  public function __construct( $url, $session, $headers = "Content-Type:application/json+nxrequest") {
    $this->urlLoggedIn = str_replace("http://", "", str_replace("https://", "", $url));
    if (strpos($url,'https') !== false) {
      $this->urlLoggedIn = "https://" . $session . "@" . $this->urlLoggedIn;
    } elseif(strpos($url,'http') !== false) {
      $this->urlLoggedIn = "http://" . $session . "@" . $this->urlLoggedIn;
    } else {
      throw Exception;
    }
    $this->headers = $headers;

    return $this;
  }

  /**
   * newRequest function
   *
   * Create a request from a session
   *
   * @var        $requestType : type of request you want to execute (such as Document.Create
   *        for exemple)
   * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
   */
  public function newRequest($requestType){
    $newRequest = new Nuxeo_Request($this->urlLoggedIn, $this->headers, $requestType);
    return $newRequest;
  }
}
