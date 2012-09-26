<?php
namespace Nuxeo\Utilities;

/**
 *
 * Contains Utilities suchas date wrappers
 * @author agallouin
 * @namespace Nuxeo\Utilities
 *
 */
class Utilities
{
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
    $configuration  = NAPC\Configuration::getInstance();
    $client         = new Nuxeo\PhpAutomationClient($configuration->getUrl(false));
    $session        = $client->getSession($configuration->getUsername(),$configuration->getPassword());
    $answer         = $session->newRequest("Chain.getDocContent")->set('context', 'path' . $path)->sendRequest();

    if (!isset($answer) || $answer == false)
      echo 'Answer was not received';
    else {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename='.end($eurl).'.pdf');
      readfile('tempstream');
    }
  }
}
