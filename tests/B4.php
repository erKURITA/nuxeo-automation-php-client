<!DOCTYPE html>
<html>
  <head>
    <title>B4 test php Client</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" media="screen" type="text/css" title="design" href="/tests/design.css" />
  </head>
  <body>
    <?php include 'nav.php' ?>
    <pre>Create a file at the path chosen with file path and attach the blob chosen in
    the blob path field to it.</pre>
    <form action="/B4" method="post" enctype="multipart/form-data">
      <table>
        <tr>
          <td>Blob Path</td>
          <td>
            <input type="file" name ="blobPath"/>
          </td>
        </tr>
        <tr>
          <td>File Path</td>
          <td>
<?php

  $configuration  = NAPC\Configuration::getInstance();
  $client         = new Nuxeo\PhpAutomationClient($configuration->getUrl(false));
  $session        = $client->getSession($configuration->getUsername(),$configuration->getPassword());
  $answer         = $session->newRequest("Document.Query")->set('params', 'query', "SELECT * FROM Workspace")->setSchema()->sendRequest();

  $documents = $answer->getDocumentList();
?>
            <select name="TargetDocumentPath">
<?php
  foreach($documents as $document) {
?>
              <option value="<?=$document->getPath()?>"><?= $document->getTitle()?></option>
<?php
  }
?>
            </select>
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

  /**
   *
   * AttachBlob function
   *
   * @param String $blob contains the path of the blob to load as an attachment
   * @param String $filePath contains the path of the folder where the fille holding the blob will be created
   * @param String $blobtype contains the type of the blob (given by the $_FILES['blobPath']['type'])
   */
  function attachBlob($blob = '../test.txt', $filePath = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395', $blobtype = 'application/binary')
  {
    //only works on LINUX / MAC
    // We get the name of the file to use it for the name of the document
    $ename = explode("/", $blob);

    $configuration = NAPC\Configuration::getInstance();

    $client = new Nuxeo\PhpAutomationClient($configuration->getUrl(false));

    $session = $client->getSession($configuration->getUsername(),$configuration->getPassword());

    //We create the document that will hold the file
    $answer = $session->newRequest("Document.Create")->set('input', 'doc:' . $filePath)->set('params', 'type', 'File')->set('params', 'name', end($ename))->sendRequest();

    //We upload the file
    $answer = $session->newRequest("Blob.Attach")
                ->set('params', 'document', $answer->getDocument(0)->getPath())
                ->loadBlob($blob, $blobtype)
                ->sendRequest();
  }


  if (isset($_POST) && $_POST != array()) {
    if(!isset($_FILES['blobPath']) AND $_FILES['blobPath']['error'] == 0) {
      echo 'BlobPath is empty';
      exit;
    }

    if(!isset($_POST['TargetDocumentPath']) || empty($_POST['TargetDocumentPath'])) {
      echo 'TargetDocumentPath is empty';
      exit;
    }

    if ((isset($_FILES['blobPath']) && ($_FILES['blobPath']['error'] == UPLOAD_ERR_OK))) {
      $targetPath = NAPC\Configuration::getInstance()->getTempPath();

      if (!is_dir('blobs'))
        mkdir('blobs');

      move_uploaded_file($_FILES['blobPath']['tmp_name'], $targetPath.'_'.$_FILES['blobPath']['name']);
    }

    attachBlob($targetPath.'_'.$_FILES['blobPath']['name'], $_POST['TargetDocumentPath'], $_FILES['blobPath']['type']);
    unlink($targetPath.'_'.$_FILES['blobPath']['name']);
  }

?>
  </body>
</html>
