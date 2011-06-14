<?php
/**
 * Document class
 *
 * hold a return document
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 */
class Nuxeo_Document
{

  private $_object;
  private $_properties;

  public function __construct ($newDocument = NULL) {
    $this->_object = $newDocument;
    if (is_array($this->_object) && array_key_exists('properties', $this->_object))
      $this->_properties = $this->_object['properties'];
    else
      $this->_properties = null;
  }

  public function getUid(){
    return $this->_object['uid'];
  }

  public function getPath(){
    return $this->_object['path'];
  }

  public function getType(){
    return $this->_object['type'];
  }

  public function getState(){
    return $this->_object['state'];
  }

  public function getTitle(){
    return $this->_object['title'];
  }

  public function output(){
    $value = sizeof($this->_object);

    for ($test = 0; $test < $value-1; $test++){
      echo '<td> ' . current($this->_object) . '</td>';
      next($this->_object);
    }

    if ($this->_properties !== NULL){
      $value = sizeof($this->_properties);
      for ($test = 0; $test < $value; $test++){
        echo '<td>' . key($this->_properties) . ' : ' .
        current($this->_properties) . '</td>';
        next($this->_properties);
      }
    }
  }

  public function getObject(){
    return $this->_object;
  }

  public function getProperty($schemaNamePropertyName){
    if (array_key_exists($schemaNamePropertyName, $this->_properties)){
      return $this->_properties[$schemaNamePropertyName];
    }
    else
      return null;
  }
}
