<?php
namespace Nuxeo\Request;

use Nuxeo\Document\Documents;

/**
 * Request class
 *
 * Request class contents all the functions needed to initialise a request and send it
 *
 * @author Arthur GALLOUIN for NUXEO <agallouin@nuxeo.com>
 * @namespace Nuxeo\Request
 *
 */
class Request
{
    private $finalRequest;
    private $url;
    private $headers;
    private $method;
    private $iterationNumber;
    private $headerNxSchemas;
    private $blobList;
    private $xNXVoidOperation;

    /**
     * @param $url
     * @param $requestId
     * @param string $headers
     */
    public function __construct($url, $requestId, $headers = "Content-Type:application/json+nxrequest")
    {
        $this->url = $url . "/" . $requestId;
        $this->headers = $headers;
        $this->finalRequest = '{}';
        $this->method = 'POST';
        $this->iterationNumber = 0;
        $this->headerNxSchemas = 'X-NXDocumentProperties:';
        $this->blobList = null;
        $this->xNXVoidOperation = 'X-NXVoidOperation: true';
    }

    /**
     * setX-NXVoidOperation header
     *
     * This header is used for the blob upload, it's noticing if the blob must be send back to the
     * client. If not used, i might be great to not using it because it will save time and connection
     * cappacity
     *
     * @param string $headerValue Value taken by the header
     * @author Arthur GALLOUIN for NUXEO <agallouin@nuxeo.com>
     */
    public function setXNXVoidOperation($headerValue = '*')
    {
        $this->xNXVoidOperation = 'X-NXVoidOperation:' . $headerValue;
    }

    /**
     * setSchema function
     *
     * Set the schemas in order to obtain file properties
     *
     * @param string $schema Name the schema you want to obtain
     * @author Arthur GALLOUIN for NUXEO <agallouin@nuxeo.com>
     * @return $this
     */
    public function setSchema($schema = '*')
    {
        $this->headers = array($this->headers, $this->headerNxSchemas . $schema);
        return $this;
    }

    /**
     * set function
     *
     * This function is used to load data in the request (such as input, context and params fields)
     *
     * @param string $requestType Contains name of the field
     * @param string $requestContentOrVarName Contains the name of the var or the content of the field in the case of
     * an input field
     * @param string $requestVarVallue Value of the var defined in $requestContentTypeOrVarName (if needed)
     * @author Arthur GALLOUIN for NUXEO <agallouin@nuxeo.com>
     * @return $this
     */
    public function set($requestType, $requestContentOrVarName, $requestVarVallue = null)
    {
        if ($requestVarVallue !== null) {
            if ($this->iterationNumber === 0) {
                $this->finalRequest = array(
                    $requestType => array($requestContentOrVarName => $requestVarVallue)
                );
            } elseif ($this->iterationNumber === 1) {
                $this->finalRequest[$requestType] = array($requestContentOrVarName => $requestVarVallue);
            } elseif ($this->iterationNumber === 2) {
                $this->finalRequest[$requestType][$requestContentOrVarName] = $requestVarVallue;
            }
            $this->iterationNumber = 2;
        } else {
            if ($this->iterationNumber === 0) {
                $this->finalRequest = array(
                    $requestType => $requestContentOrVarName
                );
            } else {
                $this->finalRequest[$requestType] = $requestContentOrVarName;
            }
            if ($this->iterationNumber === 0) {
                $this->iterationNumber = 1;
            }
        }

        return $this;
    }

    /**
     * multiPart function
     *
     * This function is used to send a multipart request (blob + request) to Nuxeo EM, such as the
     * attachBlob request
     *
     * @author Arthur GALLOUIN for NUXEO <agallouin@nuxeo.com>
     */
    private function multiPart()
    {
        if (sizeof($this->blobList) > 1 && !isset($this->finalRequest['params']['xpath'])) {
            $this->finalRequest['params']['xpath'] = 'files:files';
        }

        $this->finalRequest = json_encode($this->finalRequest);

        $this->finalRequest = str_replace('\/', '/', $this->finalRequest);

        $this->headers = array($this->headers, 'Content-ID: request');

        $requestheaders = 'Content-Type: application/json+nxrequest; charset=UTF-8' . "\r\n" .
            'Content-Transfer-Encoding: 8bit' . "\r\n" .
            'Content-ID: request' . "\r\n" .
            'Content-Length:' . strlen($this->finalRequest) . "\r\n" . "\r\n";

        $boundary = '====Part=' . time() . '=' . (int)rand(0, 1000000000) . '===';
        $data = "--" . $boundary . "\r\n" .
            $requestheaders .
            $this->finalRequest . "\r\n" . "\r\n";

        foreach ($this->blobList as $blob) {
            $data = $data . "--" . $boundary . "\r\n";

            $blobheaders = 'Content-Type:' . $blob[1] . "\r\n" .
                'Content-ID: input' . "\r\n" .
                'Content-Transfer-Encoding: binary' . "\r\n" .
                'Content-Disposition: attachment;filename=' . $blob[0] .
                "\r\n" . "\r\n";

            $data = "\r\n" . $data .
                $blobheaders .
                $blob[2] . "\r\n" . "\r\n";
        }

        $data = $data . "--" . $boundary . "--";
        $final = array(
            'http' => array(
                'method' => 'POST',
                'content' => $data,
                'header' => 'Accept: application/json+nxentity, */*' . "\r\n" .
                'Content-Type: multipart/related;boundary="' . $boundary .
                '";type="application/json+nxrequest";start="request"' .
                "\r\n" . $this->xNXVoidOperation
            )
        );

        $final = stream_context_create($final);
        $filep = fopen($this->url, 'rb', false, $final);
        $answer = stream_get_contents($filep);
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
     * @return $this
     */
    public function loadBlob($address, $contentType = 'application/binary')
    {
        if (!$this->blobList) {
            $this->blobList = array();
        }
        $eaddress = explode("/", $address);

        $filep = fopen($address, "r");

        if (!$filep) {
            echo 'error loading the file';
        }

        $futurBlob = stream_get_contents($filep);
        $temp = str_replace(" ", "", end($eaddress));
        $this->blobList[] = array($temp, $contentType, print_r($futurBlob, true));

        return $this;
    }

    /**
     * SendRequest function
     *
     * This function is used to send any kind of request to Nuxeo EM
     *
     * @author Arthur GALLOUIN for NUXEO <agallouin@nuxeo.com>
     *
     * @return mixed|null|\Nuxeo\Document\Documents|string
     */
    public function sendRequest()
    {
        if (!$this->blobList) {
            $documents = null;

            $this->finalRequest = json_encode($this->finalRequest);
            $this->finalRequest = str_replace('\/', '/', $this->finalRequest);
            $params = array(
                'http' => array(
                    'method' => $this->method,
                    'content' => $this->finalRequest
                ));

            if ($this->headers !== null) {
                $params['http']['header'] = $this->headers;
            }

            $context = stream_context_create($params);
            $filep = fopen($this->url, 'rb', false, $context);

            if ($filep === false) {
                echo "<pre>";
                echo "<hr />";
                echo "<h2>Server request</h2>\n";
                var_dump($params);
                var_dump($this);
                echo "<hr />";
                echo "</pre>";
            } else {
                $answer = stream_get_contents($filep);

                if (null == json_decode($answer, true)) {
                    $documents = $answer;
                    file_put_contents("tempstream", $answer);
                } else {
                    $answer = json_decode($answer, true);
                    $documents = new Documents($answer);
                }
            }

            return $documents;
        } else {
            return $this->multiPart();
        }
    }
}
