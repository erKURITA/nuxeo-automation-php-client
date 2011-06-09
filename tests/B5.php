<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <title>B4 test php Client</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" media="screen" type="text/css" title="Design" href="design.css" />
    </head>
    <body>
      <form action="B5.php" method="post">
      <table>
          <tr><td>File Path</td><td><input type="text" name ="path"/></td></tr>
          <tr><td>Blob Type</td><td><input type="text" name ="type"/></td></tr>
          <tr><td><input type="submit" value="Submit"/></td></tr>
        </table>
      </form><?php

  include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
  
  function GetBlob($path = '/default-domain/workspaces/jkjkj/test2.rtf', $blobtype = 'application/binary') {
    $eurl = explode("/", $path);
    
    $client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
  
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
  
  if(!isset($_POST['path'])){
    echo 'path is empty';
    exit;
  }
  if(!isset($_POST['type']))
    GetBlob($_POST['path']);
  else
    GetBlob($_POST['path'], $_POST['type']);
?></body></html>
