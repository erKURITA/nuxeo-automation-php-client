<!DOCTYPE html>
<html>
  <head>
    <title>B5 test php Client</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" media="screen" type="text/css" title="design" href="/tests/design.css" />
  </head>
  <body>
    <?php include 'nav.php' ?>
    <br />
    <form action="/B5" method="post">
      <table style="width: auto">
        <tr>
          <td>File Path</td>
          <td>
            <input type="text" name ="path"/>
          </td>
        </tr>
        <tr>
          <td>Blob Type</td>
          <td>
            <input type="text" name ="type"/>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <input type="submit" value="Submit"/>
          </td>
        </tr>
      </table>
    </form>
<?php

  function GetBlob($path = '/default-domain/workspaces/jkjkj/test2.rtf', $blobtype = 'application/binary')
  {
    $eurl           = explode("/", $path);
    $configuration  = NAPC\Configuration::getInstance();
    $client         = new Nuxeo\PhpAutomationClient($configuration->getUrl(false));
    $session        = $client->getSession($configuration->getUsername(),$configuration->getPassword());
    $answer         = $session->newRequest("Blob.Get")->set('input', 'doc: ' . $path)->sendRequest();

    if (!isset($answer) || $answer == false)
      echo '$answer is not set';
    else {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename='.end($eurl).'.pdf');
      readfile('tempstream');
    }
  }

  if (isset($_POST) && $_POST != array()) {
    if(!isset($_POST['path'])) {
      echo 'path is empty';
      exit;
    }
    if(!isset($_POST['type']))
      GetBlob($_POST['path']);
    else
      GetBlob($_POST['path'], $_POST['type']);
  }
?>
  </body>
</html>
