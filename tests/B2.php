<!DOCTYPE html>
<html>
  <head>
    <title>B2 test php Client</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" media="screen" type="text/css" title="Designtab" href="designtab.css" />
  </head>
  <body>
    <?php include 'nav.php' ?>
    <pre>Execute a SELECT * FROM Document WHERE ecm:fulltext = '". $research ."' query to Nuxeo.</pre>
    <form action="B2.php" method="post">
      <pre>
        Search<input type="text" name ="research"/><br /> <br />
        <input type="submit" value="Submit"/>
      </pre>
    </form>
    <br/>
<?php

  include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');

  function fullTextSearch($research) {

    require_once '../config.php';

    $client = new PhpAutomationClient($configuration->getUrl(false));

    $session = $client->getSession('Administrator','Administrator');

    $answer = $session->newRequest("Document.Query")->set('params', 'query', "SELECT * FROM Document WHERE ecm:fulltext = '". $research ."'")->sendRequest();

    $documents = $answer->getDocumentList();
    ?>
    <table>
      <thead>
        <tr>
          <th>uid</th>
          <th>Path</th>
          <th>Type</th>
          <th>State</th>
          <th>Title</th>
          <th>Download as PDF</th>
        </tr>
      </thead>
      <tbody>
<?php
    foreach($documents as $document)
    {
?>
        <tr>
          <td><pre><?= $document->getUid() ?></pre></td>
          <td><pre><?= $document->getPath() ?></pre></td>
          <td><pre><?= $document->getType() ?></pre></td>
          <td><pre><?= $document->getState() ?></pre></td>
          <td><pre><?= $document->getTitle() ?></pre></td>
          <td>
            <form id="test" action="../tests/B5bis.php" method="post" >
              <input type="hidden" name="data" value="<?= $document->getPath() ?>"/>
              <input type="submit" value="download"/>
            </form>
          </td>
        </tr>
<?php
    }
?>
      </tbody>
    </table><?php
  }

  if (isset($_POST) && $_POST != array())
  {
    if(!isset($_POST['research']) OR empty($_POST['research']))
      echo 'research is empty';
    else
      fullTextSearch($_POST['research']);
  }

?>
  </body>
</html>
