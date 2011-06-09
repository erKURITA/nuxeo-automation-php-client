<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <title>B3 test php Client</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" media="screen" type="text/css" title="Designtab" href="designtab.css" />
    </head>
    <body>
      Execute a dc:created query to nuxeo. Fill the blank with a date format Y/M/D
      <form action="B3.php" method="post">
      Date<input type="text" name ="date"/><br /><br />
      <input type="submit" value="Submit"/>
      </form>
      <br />

<?php

  include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
  
  function DateSearch($date) {
    $utilities = new Utilities();
    
    $client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
  
    $session = $client->getSession('Administrator','Administrator');
    
    $answer = $session->newRequest("Document.Query")->set('params', 'query', "SELECT * FROM Document WHERE dc:created >= DATE '". $utilities->dateConverterPhpToNuxeo($date) ."'")->sendRequest();
    
    $documentsArray = $answer->getDocumentList();
    $value = sizeof($documentsArray);
    echo '<table>';
    echo '<tr><TH>uid</TH><TH>Path</TH>
    <TH>Type</TH><TH>State</TH><TH>Title</TH><TH>Download as PDF</TH>';
    for ($test = 0; $test < $value; $test ++){
      echo '<tr>';
      echo '<td> ' . current($documentsArray)->getUid()  . '</td>';
      echo '<td> ' . current($documentsArray)->getPath()  . '</td>';
      echo '<td> ' . current($documentsArray)->getType()  . '</td>';
      echo '<td> ' . current($documentsArray)->getState()  . '</td>';
      echo '<td> ' . current($documentsArray)->getTitle()  . '</td>';
      echo '<td><form id="test" action="../tests/B5bis.php" method="post" >';
      echo '<input type="hidden" name="data" value="'. 
      current($documentsArray)->getPath(). '"/>';
      echo '<input type="submit" value="download"/>';
      echo '</form></td></tr>';
      next($documentsArray);
    }
    echo '</table>';
  }
  
  if(!isset($_POST['date']) OR empty($_POST['date'])){
    echo 'date is empty';
  }else{
    $top = new Utilities();
    
    $date = $top->dateConverterInputToPhp($_POST['date']);
    
    dateSearch($date);
  }
?></body></html>
