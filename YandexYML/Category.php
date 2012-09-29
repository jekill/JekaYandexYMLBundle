<?php

namespace Jeka\YandexYMLBundle\YandexYML;

use DOMDocument;
use DOMAttr;
use DOMElement;

class Category
{
    private $id = null;
    private $name = null;
    private $parentId = null;
    private $tid = null;
    private $yid = null;
    private $xml = null;

    /**
     *
     * @param int $id
     * @param string $name
     * @param int|Category $parentId
     * @param array $options
     */
    public function __construct($id, $name, $parentId = null, array $options = array())
    {
        $this->id   = $id;
        $this->name = htmlspecialchars($name);

        if ($parentId instanceof Category) {
            $this->parentId = $parentId->getId();
        } else {
            $this->parentId = $parentId;
        }
    }


    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParentId()
    {
        return $this->parentId;
    }


    /**
     * @return DOMDocument
     */
    function toXML()
    {
        $xml  = new DOMDocument();
        $xcat = $xml->appendChild(new DOMElement('category', $this->name));
        $xcat->appendChild(new DOMAttr('id', $this->id));
        if ($this->parentId) {
            $xcat->appendChild(new DOMAttr('parentId', $this->parentId));
        }

        return $xml;
    }
}
