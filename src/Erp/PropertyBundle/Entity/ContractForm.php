<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\ContractSection;

/**
 * ContractForm
 *
 * @ORM\Table(name="contract_forms")
 * @ORM\Entity(repositoryClass="Erp\PropertyBundle\Repository\ContractFormRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ContractForm
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(
     *      targetEntity="\Erp\PropertyBundle\Entity\Property",
     *      inversedBy="contractForm"
     * )
     * @ORM\JoinColumn(
     *      name="property_id",
     *      referencedColumnName="id"
     * )
     */
    protected $property;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_default", type="boolean")
     */
    protected $isDefault;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_published", type="boolean")
     */
    protected $isPublished;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    protected $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime")
     */
    protected $updatedDate;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Erp\PropertyBundle\Entity\ContractSection",
     *      mappedBy="contractForm",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     */
    protected $contractSections;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contractSections = new ArrayCollection();
    }

    /**
     * Clone
     */
    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
            $this->setIsDefault(false);
            $this->setIsPublished(false);

            $contractSections = $this->getContractSections();

            if ($contractSections) {
                foreach ($contractSections as $contractSection) {
                    /** @var ContractSection $contractSectionClone */
                    $contractSectionClone = clone $contractSection;
                    $contractSectionClone->setContractForm($this);

                    $contractSections->add($contractSectionClone);
                }
            }
        }
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return ContractForm
     */
    private function setId($id = null)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isDefault
     *
     * @param boolean $isDefault
     *
     * @return ContractForm
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return boolean
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Set createdDate
     *
     * @ORM\PrePersist
     */
    public function setCreatedDate()
    {
        $this->createdDate = new \DateTime();
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set updatedDate
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return ContractForm
     */
    public function setUpdatedDate()
    {
        $this->updatedDate = new \DateTime();
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Set property
     *
     * @param Property $property
     *
     * @return ContractForm
     */
    public function setProperty(Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Add contractSection
     *
     * @param ContractSection $contractSection
     *
     * @return ContractForm
     */
    public function addContractSection(ContractSection $contractSection)
    {
        $this->contractSections[] = $contractSection;

        return $this;
    }

    /**
     * Remove contractSection
     *
     * @param ContractSection $contractSection
     */
    public function removeContractSection(ContractSection $contractSection)
    {
        $this->contractSections->removeElement($contractSection);
    }

    /**
     * Get contractSections
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContractSections()
    {
        return $this->contractSections;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return ContractForm
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }
}
