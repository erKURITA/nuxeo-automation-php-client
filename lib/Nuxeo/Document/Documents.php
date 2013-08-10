<?php
namespace Nuxeo\Document;

/**
 * Class Documents
 *
 * Hold an Array of Document
 *
 * @author Arthur GALLOUIN for NUXEO <agallouin@nuxeo.com>
 * @package Nuxeo\Document
 */
class Documents
{
    /** @var array */
    private $documentsList;

    /**
     * @param $newDocList
     * @throws \Exception
     *
     */
    public function __construct($newDocList)
    {
        $this->documentsList = null;
        if (!empty($newDocList['entries'])) {
            foreach ($newDocList['entries'] as $docEntry) {
                $this->documentsList[] = new Document($docEntry);
            }
        } elseif (!empty($newDocList['uid'])) {
            $this->documentsList[] = new Document($newDocList);
        } elseif (is_array($newDocList)) {
            echo "<pre>";
            var_dump($this);
            echo "<hr />";
            var_dump($newDocList);
            echo "</pre>";
            throw new \Exception('file not found');
        }
    }

    /**
     *
     */
    public function output()
    {
        echo '
        <table>
            <thead>
            <tr>
                <TH>Entity-type</TH>
                <TH>Repository</TH>
                <TH>uid</TH>
                <TH>Path</TH>
                <TH>Type</TH>
                <TH>State</TH>
                <TH>Title</TH>
                <TH>Download as PDF</TH>
            </tr>
            </thead>
            <tbody>';

        /** @var Document $document */
        foreach ($this->documentsList as $document) {
            echo '
            <tr>
                '.$document->output().'
                <td>
                    <form id="test" action="../tests/B5bis.php" method="post">
                        <input type="hidden" name="a_recup" value="'.$document->getPath().'"/>
                        <input type="submit" value="download"/>
                    </form>
                </td>
            </tr>';
        }
        echo '
            </tbody>
        </table>';
    }

    /**
     * @param $number
     * @return null
     */
    public function getDocument($number)
    {
        $value = sizeof($this->documentsList);
        if ($number < $value && $number >= 0 && array_key_exists($number, $this->documentsList)) {
            return $this->documentsList[$number];
        } else {
            return null;
        }
    }

    /**
     * @return null
     */
    public function getDocumentList()
    {
        return $this->documentsList;
    }
}
