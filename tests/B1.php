<!DOCTYPE html>
<html>
  <head>
    <title>B1 test php Client</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" media="screen" type="text/css" title="design" href="/tests/design.css" />
  </head>
  <body>
    <?php include 'nav.php' ?>
    <pre>
      Execute a SELECT * FROM Document WHERE ecm:path = Path query to Nuxeo
      and print all the document porperties.
      fill the path field with a correct Path and the Schema field
      with the type of schema to output (if left blank, print all properties)
      </pre>
      <form action="/B1" method="post">
        <pre>
          <label>Path<input type="text" name ="path"/></label>
          <label>Schema<input type="text" name ="schema"/></label>
          <input type="submit" value="Submit"/>
        </pre>
      </form>
      <br />
<?php

  function openDocumentPropeties($path, $propertiesSchema = '*') {

    $configuration = Configuration::getInstance();

    $client = new Nuxeo_PhpAutomationClient($configuration->getUrl(false));

    $session = $client->getSession($configuration->getUsername(),$configuration->getPassword());

    $answer = $session->newRequest("Document.Query")->set('params', 'query', "SELECT * FROM Document WHERE ecm:path = '". $path ."'")->setSchema($propertiesSchema)->sendRequest();

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
          <th>Property 1</th>
          <th>Property 2</th>
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
          <td><pre><?= $document->getProperty('dc:description') ?></pre></td>
          <td><pre><?= $document->getProperty('dc:creator') ?></pre></td>
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
    if(!isset($_POST['path']) || empty($_POST['path'])){
      echo 'path is empty';
    }
    else{
      if(isset($_POST['schema']) && !empty($_POST['schema']))
        openDocumentPropeties($_POST['path'], $_POST['schema']);
      else
        openDocumentPropeties($_POST['path']);
    }
  }

?>
  </body>
</html>
