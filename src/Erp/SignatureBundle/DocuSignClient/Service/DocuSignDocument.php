<?php
namespace Erp\SignatureBundle\DocuSignClient\Service;

/**
 * Class DocuSignDocument
 *
 * @package Erp\SignatureBundle\DocuSignClient
 */
class DocuSignDocument extends DocuSignModel
{
    private $name;
    private $id;
    private $content;

    /**
     * @param $name
     * @param $id
     * @param $content
     */
    public function __construct($name, $id, $content)
    {
        if (isset($name)) {
            $this->name = $name;
        }

        if (isset($id)) {
            $this->id = $id;
        }

        if (isset($content)) {
            $this->content = $content;
        }
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}
