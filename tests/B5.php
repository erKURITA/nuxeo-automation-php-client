<!DOCTYPE html>
<html>
  <head>
    <title>B4 test php Client</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" media="screen" type="text/css" title="Design" href="design.css" />
  </head>
  <body>
    <?php include 'nav.php' ?>
    <form action="B5.php" method="post">
      <table>
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
          <td>
            <input type="submit" value="Submit"/>
          </td>
        </tr>
      </table>
    </form><?php

  include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');

  function GetBlob($path = '/default-domain/workspaces/jkjkj/test2.rtf', $blobtype = 'application/binary') {
    $eurl = explode("/", $path);

    require_once '../config.php';

    $client = new PhpAutomationClient($configuration->getUrl(false));

    $session = $client->GetSession('Administrator','Administrator');

    $answer = $session->NewRequest("Blob.Get")->Set('input', 'doc: ' . $path)->SendRequest();

    if (!isset($answer) OR $answer == false)
      echo '$answer is not set';
    else{
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.end($eurl).'.pdf');
        readfile('tempstream');
    }
  }

  if (isset($_POST) && $_POST != array())
  {
    if(!isset($_POST['path'])){
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
