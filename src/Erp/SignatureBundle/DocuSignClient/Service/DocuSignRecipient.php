<?php
namespace Erp\SignatureBundle\DocuSignClient\Service;

/**
 * Class DocuSignRecipient
 *
 * @package Erp\SignatureBundle\DocuSignClient\Service
 */
class DocuSignRecipient extends DocuSignModel
{
    private $routingOrder;
    private $id;
    private $name;
    private $email;
    private $clientId;
    private $type;
    private $tabs;

    /**
     * @param        $routingOrder
     * @param        $id
     * @param        $name
     * @param        $email
     * @param null   $clientId
     * @param string $type
     * @param null   $tabs
     */
    public function __construct($routingOrder, $id, $name, $email, $clientId = null, $type = 'signers', $tabs = null)
    {
        if (isset($routingOrder)) {
            $this->routingOrder = $routingOrder;
        }

        if (isset($id)) {
            $this->id = $id;
        }

        if (isset($name)) {
            $this->name = $name;
        }

        if (isset($email)) {
            $this->email = $email;
        }

        if (isset($type)) {
            $this->type = $type;
        }

        // Ensure that a client id only gets assigned to allowed recipient types.
        if (isset($clientId)) {
            switch ($type) {
                case 'signers':
                case 'agents':
                case 'intermediaries':
                case 'editors':
                case 'certifiedDeliveries':
                    $this->clientId = $clientId;
                    break;
            }
        }

        if (isset($tabs) && is_array($tabs)) {
            foreach ($tabs as $tabType => $tab) {
                foreach ($tab as $singleTab) {
                    $this->setTab($tabType, $singleTab);
                }
            }
        }
    }

    /**
     * @param $routingOrder
     */
    public function setRoutingOrder($routingOrder)
    {
        $this->routingOrder = $routingOrder;
    }

    /**
     * @return mixed
     */
    public function getRoutingOrder()
    {
        return $this->routingOrder;
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
     * @param $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return null
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getTabs()
    {
        return $this->tabs;
    }

    /**
     * @param $tabType
     * @param $tabLabel
     *
     * @return array
     */
    public function getTab($tabType, $tabLabel)
    {
        foreach ($this->tabs[$tabType] as $tab) {
            if ($tab['tabLabel'] == $tabLabel) {
                return array($tabType => $tab);
            }
        }
    }

    /**
     * @param $tabType
     * @param $tab
     */
    public function setTab($tabType, $tab)
    {
        //.. construct tab array
        switch ($tabType) {
            case 'approveTabs':
            case 'checkboxTabs':
            case 'companyTabs':
            case 'dateSignedTabs':
            case 'dateTabs':
            case 'declineTabs':
            case 'emailTabs':
            case 'emailAddressTabs':
            case 'envelopeIdTabs':
            case 'firstNameTabs':
            case 'formulaTabs':
            case 'fullNameTabs':
            case 'initialHereTabs':
            case 'lastNameTabs':
            case 'noteTabs':
            case 'listTabs':
            case 'numberTabs':
            case 'radioGroupTabs':
            case 'signHereTabs':
            case 'signerAttachmentTabs':
            case 'ssnTabs':
            case 'textTabs':
            case 'titleTabs':
            case 'zipTabs':
                $this->tabs[$tabType][] = $tab;
                break;
        }
    }

    /**
     * @param $tabType
     * @param $tabLabel
     */
    public function unsetTab($tabType, $tabLabel)
    {
        foreach ($this->tabs[$tabType] as &$tab) {
            if ($tab['tabLabel'] == $tabLabel) {
                unset($tab);
            }
        }
    }
}
