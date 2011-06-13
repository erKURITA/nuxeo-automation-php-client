<?php

  include ('../NuxeoAutomationClient/NuxeoAutomationUtilities.php');

  /**
   * phpAutomationClient class
   *
   * Class which initializes the php client with an URL
   *
   * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
   */
  class PhpAutomationClient {
    private $url;

    public function PhpAutomationCLient($url = 'http://localhost:8080/nuxeo/site/automation'){
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
      $session = new Session($this->url, $this->session);
      return $session;
    }
  }

  /**
   * Session class
   *
   * Class which stocks username,password, and open requests
   *
   * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
   */
  class Session {

    private $urlLoggedIn;
    private $headers;
    private $requestContent;

    public function Session( $url, $session, $headers = "Content-Type:application/json+nxrequest") {
      $this->urlLoggedIn = str_replace("http://", "", str_replace("https://", "", $url));
      if (strpos($url,'https') !== false){
        $this->urlLoggedIn = "https://" . $session . "@" . $this->urlLoggedIn;
      }elseif(strpos($url,'http') !== false){
        $this->urlLoggedIn = "http://" . $session . "@" . $this->urlLoggedIn;
      }else{
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
      $newRequest = new Request($this->urlLoggedIn, $this->headers, $requestType);
      return $newRequest;
    }
  }

  /**
   * Document class
   *
   * hold a return document
   *
   * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
   */
  class Document {

    Private $object;
    Private $properties;

    Public function Document ($newDocument = NULL) {
      $this->object = $newDocument;
      if (is_array($this->object) && array_key_exists('properties', $this->object))
        $this->properties = $this->object['properties'];
      else
        $this->properties = null;
    }

    public function getUid(){
      return $this->object['uid'];
    }

    public function getPath(){
      return $this->object['path'];
    }

    public function getType(){
      return $this->object['type'];
    }

    public function getState(){
      return $this->object['state'];
    }

    public function getTitle(){
      return $this->object['title'];
    }

    Public function output(){
      $value = sizeof($this->object);

      for ($test = 0; $test < $value-1; $test++){
        echo '<td> ' . current($this->object) . '</td>';
        next($this->object);
      }

      if ($this->properties !== NULL){
        $value = sizeof($this->properties);
        for ($test = 0; $test < $value; $test++){
          echo '<td>' . key($this->properties) . ' : ' .
          current($this->properties) . '</td>';
          next($this->properties);
        }
      }
    }

    public function getObject(){
      return $this->object;
    }

    public function getProperty($schemaNamePropertyName){
      if (array_key_exists($schemaNamePropertyName, $this->properties)){
        return $this->properties[$schemaNamePropertyName];
      }
      else
        return null;
    }
  }

  /**
   * Documents class
   *
   * hold an Array of Document
   *
   * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
   */
  class Documents {

    private $documentsList;

    public function documents($newDocList){
      $this->documentsList = null;
      $test = true;
      if (!empty($newDocList['entries'])){
        while (false !== $test) {
          $this->documentsList[] = new Document(current($newDocList['entries']));
          $test = each($newDocList['entries']);
        }
        $test = sizeof($this->documentsList);
        unset($this->documentsList[$test-1]);
      }
      elseif(!empty($newDocList['uid'])){
        $this->documentsList[] = new Document($newDocList);
      }elseif(is_array($newDocList)){
        echo "<pre>";
        var_dump($this);
        var_dump($newDocList);
        echo "</pre>";
        echo 'file not found';
      }else{
        return $newDocList;
      }
    }

    public function output(){
?>
      <table>
        <thead>
          <tr>
            <TH>Entity-type</TH>
            <TH>Repository</TH>
            <TH>uid</TH>
            <TH>Path</TH>
            <TH>Type</TH>
            <TH>State</TH>
            <TH>Title</TH>
            <TH>Download as PDF</TH>
          </tr>
        </thead>
        <tbody>
<?php
      foreach($this->documentsList as $document){
?>
          <tr>
            <?= $document->output(); ?>
            <td>
              <form id="test" action="../tests/B5bis.php" method="post" >';
                <input type="hidden" name="a_recup" value="<?=$document->getPath()?>"/>
                <input type="submit" value="download"/>
              </form>
            </td>
          </tr>';
<?php
      }
?>
        </tbody>
      </table>
<?php
    }

    public function getDocument($number){
      $value = sizeof($this->documentsList);
      if ($number < $value AND $number >= 0)
        return $this->documentsList[$number];
      else
        return null;
    }

    public function getDocumentList(){
      return $this->documentsList;
    }
  }

  /**
   *
   * Contains Utilities such as date wrappers
   * @author agallouin
   *
   */
  class Utilities{
    private $ini;

    public function dateConverterPhpToNuxeo($date){

      $time = '';
      try {
        $datetime = new DateTime($date);
        $time = $datetime->format('Y-m-d');
      } catch (Exception $e) {
        $time = '';
      }

      return $time;
    }

    public function dateConverterNuxeoToPhp($date){
      $newDate = explode('T', $date);
      $phpDate = new DateTime($newDate[0]);
      return $phpDate;
    }

    public function dateConverterInputToPhp($date){

      /**
       * If given a date from user input and DateTime fails to parse it correctly,
       * then it must not be correct, thus we can safely exit.
       */
      try {
        $datetime = new DateTime($date);
      } catch (Exception $e) {
        echo 'date not correct';
        exit;
      }

      $phpDate = $datetime->format('Y-m-d');

      return $phpDate;
    }

    /**
     *
     * Function Used to get Data from Nuxeo, such as a blob. MUST BE PERSONALISED. (Or just move the
     * headers)
     *
     *
     * @param $path path of the file
     */
    function getFileContent($path = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395') {

      $eurl = explode("/", $path);

      $client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');

      $session = $client->getSession('Administrator','Administrator');

      $answer = $session->newRequest("Chain.getDocContent")->set('context', 'path' . $path)
            ->sendRequest();

      if (!isset($answer) OR $answer == false)
        echo '$answer is not set';
      else{
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
          header('Content-Disposition: attachment; filename='.end($eurl).'.pdf');
          readfile('tempstream');
      }
    }
  }

?>
