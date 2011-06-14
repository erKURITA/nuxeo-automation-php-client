<?php

/**
 *
 * Contains Utilities such as date wrappers
 * @author agallouin
 *
 */
class Nuxeo_Utilities
{
  private $_ini;

  public function dateConverterPhpToNuxeo($date)
  {

    $time = '';
    try {
      $datetime = new DateTime($date);
      $time = $datetime->format('Y-m-d');
    } catch (Exception $e) {
      $time = '';
    }

    return $time;
  }

  public function dateConverterNuxeoToPhp($date)
  {
    $newDate = explode('T', $date);
    $phpDate = new DateTime($newDate[0]);
    return $phpDate;
  }

  public function dateConverterInputToPhp($date)
  {
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
   * @author agallouin
   * @param $path path of the file
   */
  public function getFileContent($path = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395')
  {
    $eurl           = explode("/", $path);
    $configuration  = Configuration::getInstance();
    $client         = new Nuxeo_PhpAutomationClient($configuration->getUrl(false));
    $session        = $client->getSession($configuration->getUsername(),$configuration->getPassword());
    $answer         = $session->newRequest("Chain.getDocContent")->set('context', 'path' . $path)->sendRequest();

    if (!isset($answer) OR $answer == false)
      echo '$answer is not set';
    else {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename='.end($eurl).'.pdf');
      readfile('tempstream');
    }
  }
}
