<?php

namespace Erp\VendorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VendorCreate
 *
 * @ORM\Table(name="vendor_create")
 * @ORM\Entity(repositoryClass="Erp\VendorBundle\Repository\VendorCreateRepository")
 */
class VendorCreate {

    const REPOSITORY = 'ErpVendorBundle:VendorCreate';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Erp\VendorBundle\Entity\VendorEdit",
     *      mappedBy="vendorCreate",
     *      cascade={"persist", "remove"}
     * )
     * * @ORM\OrderBy({"createdDate"="DESC"})
     */
    private $vendorEdits;

    /**
     * Constructor
     */
    public function __construct() {
        $this->vendorEdits = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __destruct() {
        unset($this->vendorEdits);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return VendorCreate
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return VendorCreate
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Add vendorEdit
     *
     * @param \Erp\VendorBundle\Entity\VendorEdit $vendorEdit
     *
     * @return VendorCreate
     */
    public function addVendorEdit(\Erp\VendorBundle\Entity\VendorEdit $vendorEdit) {
        $this->vendorEdits[] = $vendorEdit;
        
        $vendorEdit->setVendorCreate($this);

        return $this;
    }

    /**
     * Remove vendorEdit
     *
     * @param \Erp\VendorBundle\Entity\VendorEdit $vendorEdit
     */
    public function removeVendorEdit(\Erp\VendorBundle\Entity\VendorEdit $vendorEdit) {
        $this->vendorEdits->removeElement($vendorEdit);
    }

    /**
     * Get vendorEdits
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVendorEdits() {
        return $this->vendorEdits;
    }
    
    /**
     * 
     * @return \Erp\VendorBundle\Entity\VendorEdit
     */
    public function getLastVendorEdit() {
        return $this->vendorEdits->first();
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasVendorEdit() {
        return !($this->vendorEdits->isEmpty());
    }

}
