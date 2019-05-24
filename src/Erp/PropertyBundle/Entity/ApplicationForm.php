<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\ApplicationSection;


/**
 * ApplicationForm
 *
 * @ORM\Table(name="application_forms")
 * @ORM\Entity(repositoryClass="Erp\PropertyBundle\Repository\ApplicationFormRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ApplicationForm
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
     * @var boolean
     *
     * @ORM\Column(name="is_default", type="boolean")
     */
    protected $isDefault = false;

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
     * @ORM\OneToOne(
     *      targetEntity="\Erp\PropertyBundle\Entity\Property",
     *      inversedBy="applicationForm"
     * )
     * @ORM\JoinColumn(
     *      name="property_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $property;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Erp\PropertyBundle\Entity\ApplicationSection",
     *      mappedBy="applicationForm",
     *      cascade={"persist", "remove"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinColumn(
     *      name="application_form_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $applicationSections;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_no_fee", type="boolean")
     */
    protected $noFee = true;

    /**
     * @var float
     *
     * @ORM\Column(name="fee", type="float", nullable=true)
     */
    protected $fee;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->applicationSections = new ArrayCollection();
    }

    /**
     * Clone
     */
    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
            $this->setIsDefault(false);
            $applicationSections = $this->getApplicationSections();
            $applicationSectionsArray = new ArrayCollection();
            foreach ($applicationSections as $applicationSection)
            {
                /* @var ApplicationSection $applicationSection */
                $applicationSectionClone = clone $applicationSection;
                $applicationSectionClone->setApplicationForm($this);
                $applicationSectionsArray->add($applicationSectionClone);
            }
            $this->applicationSections = $applicationSectionsArray;
        }
    }


    /**
     * Set id
     *
     * @param string $id
     *
     * @return ApplicationForm
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
     * @return ApplicationForm
     */
    public function setIsDefault($isDefault = false)
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
     * @return ApplicationForm
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
     * Add applicationSection
     *
     * @param ApplicationSection $applicationSection
     *
     * @return ApplicationForm
     */
    public function addApplicationSection(ApplicationSection $applicationSection)
    {
        $this->applicationSections[] = $applicationSection;

        return $this;
    }

    /**
     * Remove applicationSection
     *
     * @param ApplicationSection $applicationSection
     */
    public function removeApplicationSection(ApplicationSection $applicationSection)
    {
        $this->applicationSections->removeElement($applicationSection);
    }

    /**
     * Get applicationSections
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplicationSections()
    {
        return $this->applicationSections;
    }

    /**
     * Set $noFee
     *
     * @param boolean $noFee
     *
     * @return ApplicationForm
     */
    public function setNoFee($noFee)
    {
        $this->noFee = (boolean) $noFee;

        return $this;
    }

    /**
     * Get useFee
     *
     * @return boolean
     */
    public function getNoFee()
    {
        return $this->noFee;
    }

    /**
     * Set fee
     *
     * @param float $fee
     *
     * @return ApplicationForm
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get fee
     *
     * @return float
     */
    public function getFee()
    {
        return $this->fee;
    }

    public function isNoFee()
    {
        return $this->noFee;
    }

    /**
     * Set property
     *
     * @param Property $property
     *
     * @return ApplicationForm
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
}
