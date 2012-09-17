<?php
  /**
   *
   * getFileContent function
   * function used to download the blob of a file, converted into a PDF file
   * @param String $path contains the path of th file holding the blob
   */
  function getFileContent($path = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395')
  {
    $eurl           = explode("/", $path);
    $temp           = str_replace(" ", "", end($eurl));
    $configuration  = NAPC\Configuration::getInstance();
    $client         = new Nuxeo\PhpAutomationClient($configuration->getUrl(false));
    $session        = $client->getSession($configuration->getUsername(),$configuration->getPassword());
    $answer         = $session->newRequest("Chain.getDocContent")->set('context', 'path', $path)->sendRequest();

    if (!isset($answer) OR $answer == false)
      echo '$answer is not set';
    else {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename='. $temp .'.pdf');
      readfile('tempstream');
    }
  }

  if (!isset($_POST['data']) OR empty($_POST['data']))
    echo 'error';
  else
    getFileContent($_POST['data']);
?>
