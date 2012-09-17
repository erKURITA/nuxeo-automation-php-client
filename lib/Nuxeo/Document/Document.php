<?php
namespace Nuxeo\Document;
/**
 * Document class
 *
 * hold a return document
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 * @namespace Nuxeo\Document
 */
class Document
{
  private $_object;
  private $_properties;

  public function __construct ($newDocument = NULL)
  {
    $this->_object = $newDocument;
    if (is_array($this->_object) && array_key_exists('properties', $this->_object))
      $this->_properties = $this->_object['properties'];
    else
      $this->_properties = null;
  }

  public function getUid()
  {
    return $this->_object['uid'];
  }

  public function getPath()
  {
    return $this->_object['path'];
  }

  public function getType()
  {
    return $this->_object['type'];
  }

  public function getState()
  {
    return $this->_object['state'];
  }

  public function getTitle()
  {
    return $this->_object['title'];
  }

  public function output()
  {
    foreach ($this->_object as $document)
      echo '<td> ' . $document . '</td>';

    if ($this->_properties !== NULL)
      foreach ($this->_object as $key => $property)
        echo '<td>'.$key.' : '.$property.'</td>';
  }

  public function getObject()
  {
    return $this->_object;
  }

  public function getProperty($schemaNamePropertyName)
  {
    if (array_key_exists($schemaNamePropertyName, $this->_properties)) {
      return $this->_properties[$schemaNamePropertyName];
    }
    else
      return null;
  }
}
