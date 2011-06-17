<?php

/**
* Request class
*
* Request class contents all the functions needed to initialise a request and send it
*
* @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
*/
class Nuxeo_Request
{

  private $_finalRequest;
  private $_url;
  private $_headers;
  private $_method;
  private $_iterationNumber;
  private $_HEADER_NX_SCHEMAS;
  private $_blobList;
  private $_X_NXVoidOperation;

  public function __construct($url, $headers = "Content-Type:application/json+nxrequest", $requestId)
  {
    $this->_url               = $url."/".$requestId;
    $this->_headers           = $headers;
    $this->_finalRequest      = '{}';
    $this->_method            = 'POST';
    $this->_iterationNumber   = 0;
    $this->_HEADER_NX_SCHEMAS = 'X-NXDocumentProperties:';
    $this->_blobList          = null;
    $this->_X_NXVoidOperation = 'X-NXVoidOperation: true';
  }

  /**
   * setX-NXVoidOperation header
   *
   * This header is used for the blob upload, it's noticing if the blob must be send back to the
   * client. If not used, i might be great to not using it because it will save time and connection
   * cappacity
   *
   * @param      $headerValue value taken by the header
   * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
   */
  public function setX_NXVoidOperation($headerValue = '*')
  {
    $this->_X_NXVoidOperation = 'X-NXVoidOperation:'.$headerValue;
  }

  /**
   * setSchema function
   *
   * Set the schemas in order to obtain file properties
   *
   * @param    $schema : name the schema you want to obtain
   * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
   */
  public function setSchema($schema = '*')
  {
    $this->_headers = array($this->_headers, $this->_HEADER_NX_SCHEMAS . $schema);
    return $this;
  }

  /**
   * set function
   *
   * This function is used to load data in the request (such as input, context and params fields)
   *
   * @param    $requestType : contents name of the field
   *         $requestContentOrVarName : contents the name of the var or the content of the field
   *                      in the case of an input field
   *         $requestVarVallue : vallue of the var define in $requestContentTypeOrVarName(if needed)
   * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
   */
  public function set($requestType, $requestContentOrVarName, $requestVarVallue =  NULL)
  {
    if ($requestVarVallue !== NULL) {
      if ($this->_iterationNumber === 0) {
        $this->_finalRequest = array(
            $requestType=> array( $requestContentOrVarName => $requestVarVallue)
          );
      } else if ($this->_iterationNumber === 1) {
        $this->_finalRequest[$requestType] = array($requestContentOrVarName => $requestVarVallue);
      } else if ($this->_iterationNumber === 2) {
        $this->_finalRequest[$requestType][$requestContentOrVarName] = $requestVarVallue;
      }
      $this->_iterationNumber = 2;
    } else {
      if ($this->_iterationNumber === 0) {
        $this->_finalRequest = array(
          $requestType => $requestContentOrVarName
        );
      } else {
        $this->_finalRequest[$requestType] = $requestContentOrVarName;
      }
      if ($this->_iterationNumber === 0)
        $this->_iterationNumber = 1;
    }

    return $this;
  }

  /**
   * multiPart function
   *
   * This function is used to send a multipart request (blob + request) to Nuxeo EM, such as the
   * attachBlob request
   *
   * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
   */
  private function _multiPart()
  {
    if (sizeof($this->_blobList) > 1 AND !isset($this->_finalRequest['params']['xpath']))
      $this->_finalRequest['params']['xpath'] = 'files:files';

    $this->_finalRequest = json_encode($this->_finalRequest);

    $this->_finalRequest = str_replace('\/', '/', $this->_finalRequest);

    $this->_headers = array($this->_headers, 'Content-ID: request');

    $requestheaders = 'Content-Type: application/json+nxrequest; charset=UTF-8'."\r\n".
                      'Content-Transfer-Encoding: 8bit'."\r\n".
                      'Content-ID: request'."\r\n".
                      'Content-Length:'.strlen($this->_finalRequest)."\r\n"."\r\n";

    $boundary = '====Part=' . time() . '='.(int)rand(0, 1000000000). '===';
    $data     = "--" . $boundary . "\r\n" .
                $requestheaders .
                $this->_finalRequest . "\r\n" ."\r\n";

    foreach($this->_blobList as $blob) {
      $data = $data . "--" . $boundary . "\r\n" ;

      $blobheaders = 'Content-Type:'.$blob[1]."\r\n".
                 'Content-ID: input'. "\r\n" .
                     'Content-Transfer-Encoding: binary'."\r\n" .
                     'Content-Disposition: attachment;filename='.$blob[0].
                     "\r\n" ."\r\n";

      $data = "\r\n". $data .
                  $blobheaders.
                  $blob[2] . "\r\n"."\r\n";
    }

    $data   = $data ."--" . $boundary."--";
    $final  = array(
      'http' => array(
        'method'  => 'POST',
        'content' => $data,
        'header'  => 'Accept: application/json+nxentity, */*'. "\r\n".
                     'Content-Type: multipart/related;boundary="'.$boundary.
                     '";type="application/json+nxrequest";start="request"'.
                     "\r\n". $this->_X_NXVoidOperation
      )
    );

    $final  = stream_context_create($final);
    $fp     = @fopen($this->_url, 'rb', false, $final);
    $answer = @stream_get_contents($fp);
    $answer = json_decode($answer, true);

    return $answer;
  }

  /**
   *
   * loadBlob function
   * Many blobs could be loaded, they will be store in a blob array
   *
   * @param $address : contains the path of the file to load
   * @param $contentType : type of the blob content (default : 'application/binary')
   */
  public function loadBlob($address, $contentType  = 'application/binary')
  {
    if(!$this->_blobList) {
      $this->_blobList = array();
    }
    $eaddress = explode("/", $address);

    $fp = fopen($address, "r");

    if (!$fp)
      echo 'error loading the file';

    $futurBlob = stream_get_contents($fp);
    $temp = str_replace(" ", "", end($eaddress));
    $this->_blobList[] = array($temp, $contentType, print_r($futurBlob, true));

    return $this;
  }

  /**
   * SendRequest function
   *
   * This function is used to send any kind of request to Nuxeo EM
   *
   * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
   */
  public function sendRequest()
  {
    if (!$this->_blobList) {
      $documents = null;

      $this->_finalRequest = json_encode($this->_finalRequest);
      $this->_finalRequest = str_replace('\/', '/', $this->_finalRequest);
      $params = array(
        'http' => array(
          'method' => $this->_method,
          'content' => $this->_finalRequest
      ));

      if ($this->_headers !== null) {
        $params['http']['header'] = $this->_headers;
      }

      $context  = stream_context_create($params);
      $fp       = @fopen($this->_url, 'rb', false, $context);
      $answer   = @stream_get_contents($fp);

      if (!isset($answer) OR $answer == false) {
        echo "<pre>";
        echo "<hr />";
        echo "<h2>Server request</h2>\n";
        var_dump($params);
        var_dump($this);
        echo "<hr />";
        echo "<h2>Server response</h2>\n";
        var_dump($answer);
        echo "</pre>";
      } else {
        if (null == json_decode($answer, true)) {
          $documents = $answer;
          file_put_contents("tempstream", $answer);
        } else {
          $answer = json_decode($answer, true);
          $documents = new Nuxeo_Documents($answer);
        }
      }

      return $documents;
    }
    else
      $this->_multiPart();
  }

}
