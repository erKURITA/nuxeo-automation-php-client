<!DOCTYPE html>
<html>
  <head>
    <title>B3 test php Client</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" media="screen" type="text/css" title="design" href="/tests/design.css" />
  </head>
  <body>
    <?php include 'nav.php' ?>
    <pre>Execute a dc:created query to nuxeo. Fill the blank with a date format Y/M/D or a date parseable by DateTime</pre>
    <form action="/B3" method="post">
      <pre>
        Date<input type="text" name ="date"/><br /><br />
        <input type="submit" value="Submit"/>
      </pre>
    </form>
    <br />

<?php

  function DateSearch($date)
  {
    $utilities      = new Nuxeo_Utilities();
    $configuration  = Configuration::getInstance();
    $client         = new Nuxeo_PhpAutomationClient($configuration->getUrl(false));
    $session        = $client->getSession($configuration->getUsername(),$configuration->getPassword());
    $answer         = $session->newRequest("Document.Query")
                        ->set('params', 'query', "SELECT * FROM Document WHERE dc:created >= DATE '". $utilities->dateConverterPhpToNuxeo($date) ."'")
                        ->sendRequest();

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
    foreach($documents as $document) {
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

  if (isset($_POST) && $_POST != array()) {
    if(!isset($_POST['date']) OR empty($_POST['date'])) {
      echo 'date is empty';
    } else {
      $top  = new Nuxeo_Utilities();
      $date = $top->dateConverterInputToPhp($_POST['date']);
      dateSearch($date);
    }
  }
?></body></html>
