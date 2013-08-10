<?php
namespace Nuxeo\Document;

/**
 * Class Document
 *
 * hold a return document
 *
 * @author  Arthur GALLOUIN for NUXEO <agallouin@nuxeo.com>
 * @package Nuxeo\Document
 */
class Document
{
    private $object;
    private $properties;

    /**
     * @param null $newDocument
     */
    public function __construct($newDocument = null)
    {
        $this->object = $newDocument;
        if (is_array($this->object) && array_key_exists('properties', $this->object)) {
            $this->properties = $this->object['properties'];
        } else {
            $this->properties = null;
        }
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->object['uid'];
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->object['path'];
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->object['type'];
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->object['state'];
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->object['title'];
    }

    /**
     *
     */
    public function output()
    {
        foreach ($this->object as $document) {
            echo '<td> '.$document.'</td>';
        }

        if ($this->properties !== null) {
            foreach ($this->object as $key => $property) {
                echo '<td>'.$key.' : '.$property.'</td>';
            }
        }
    }

    /**
     * @return null
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param $schemaNamePropertyName
     *
     * @return null
     */
    public function getProperty($schemaNamePropertyName)
    {
        if (array_key_exists($schemaNamePropertyName, $this->properties)) {
            return $this->properties[$schemaNamePropertyName];
        } else {
            return null;
        }
    }
}
