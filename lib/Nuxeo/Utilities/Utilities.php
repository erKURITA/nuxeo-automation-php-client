<?php
namespace Nuxeo\Utilities;

use NAPC\Configuration;
use Nuxeo\PhpAutomationClient;

/**
 * Class Utilities
 *
 * Contains Utilities such as date wrappers
 *
 * @author agallouin
 * @package Nuxeo\Utilities
 */
class Utilities
{
    /**
     * Function Used to get Data from Nuxeo, such as a blob. MUST BE PERSONALISED. (Or just move the
     * headers)
     *
     * @author agallouin
     *
     * @param string $path
     */
    public function getFileContent($path = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395')
    {
        $eurl           = explode("/", $path);
        $configuration  = Configuration::getInstance();
        $client         = new PhpAutomationClient($configuration->getUrl(false));
        $session        = $client->getSession($configuration->getUsername(), $configuration->getPassword());
        $answer         = $session->newRequest("Chain.getDocContent")->set('context', 'path' . $path)->sendRequest();

        if (!isset($answer) || $answer == false) {
            echo 'Answer was not received';
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.end($eurl).'.pdf');
            readfile('tempstream');
        }
    }
}
