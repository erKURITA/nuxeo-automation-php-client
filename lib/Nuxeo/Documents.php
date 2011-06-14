<?php
/**
 * Documents class
 *
 * hold an Array of Document
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 */
class Nuxeo_Documents
{
  private $_documentsList;

  public function __construct($newDocList){
    $this->_documentsList = null;
    $test = true;
    if (!empty($newDocList['entries'])){
      while (false !== $test) {
        $this->_documentsList[] = new Nuxeo_Document(current($newDocList['entries']));
        $test = each($newDocList['entries']);
      }
      $test = sizeof($this->_documentsList);
      unset($this->_documentsList[$test-1]);
    }
    elseif(!empty($newDocList['uid'])){
      $this->_documentsList[] = new Nuxeo_Document($newDocList);
    }elseif(is_array($newDocList)){
      echo "<pre>";
      var_dump($this);
      var_dump($newDocList);
      echo "</pre>";
      echo 'file not found';
    }else{
      return $newDocList;
    }
  }

  public function output(){
?>
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
      <tbody>
<?php
    foreach($this->_documentsList as $document){
?>
        <tr>
          <?= $document->output(); ?>
          <td>
            <form id="test" action="../tests/B5bis.php" method="post" >';
              <input type="hidden" name="a_recup" value="<?=$document->getPath()?>"/>
              <input type="submit" value="download"/>
            </form>
          </td>
        </tr>';
<?php
    }
?>
      </tbody>
    </table>
<?php
  }

  public function getDocument($number){
    $value = sizeof($this->_documentsList);
    if ($number < $value AND $number >= 0)
      return $this->_documentsList[$number];
    else
      return null;
  }

  public function getDocumentList(){
    return $this->_documentsList;
  }
}
