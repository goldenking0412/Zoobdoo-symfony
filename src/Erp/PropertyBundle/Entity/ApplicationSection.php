<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\ApplicationField;
use Erp\PropertyBundle\Entity\ApplicationForm;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ApplicationSection
 *
 * @ORM\Table(name="application_sections")
 * @ORM\Entity(repositoryClass="Erp\PropertyBundle\Repository\ApplicationSectionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ApplicationSection
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter your Name", groups={"ApplicationSection"})
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Name should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Name should have minimum 2 characters and maximum 255 characters",
     *     groups={"ApplicationSection"}
     * )
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer")
     */
    protected $sort;

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
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\PropertyBundle\Entity\ApplicationForm",
     *      inversedBy="applicationSections"
     * )
     * @ORM\JoinColumn(
     *      name="application_section_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $applicationForm;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Erp\PropertyBundle\Entity\ApplicationField",
     *      mappedBy="applicationSection",
     *      cascade={"persist", "remove"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinColumn(
     *      name="application_section_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $applicationFields;

    /**
     * Clone
     */
    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
            $applicationFields = $this->getApplicationFields();
            $applicationFieldsArray = new ArrayCollection();
            if ($applicationFields) {
                foreach ($applicationFields as $applicationField) {
                    /** @var ApplicationField $applicationFieldClone */
                    $applicationFieldClone = clone $applicationField;
                    $applicationFieldClone->setApplicationSection($this);
                    $applicationFieldsArray->add($applicationFieldClone);
                }
                $this->applicationFields = $applicationFieldsArray;
            }
        }
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return ApplicationSection
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
     * Set name
     *
     * @param string $name
     *
     * @return ApplicationSection
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     *
     * @return ApplicationSection
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer
     */
    public function getSort()
    {
        return $this->sort;
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
     * @return ApplicationSection
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
     * Constructor
     */
    public function __construct()
    {
        $this->applicationFields = new ArrayCollection();
    }

    /**
     * Set applicationForm
     *
     * @param ApplicationForm $applicationForm
     *
     * @return ApplicationSection
     */
    public function setApplicationForm(ApplicationForm $applicationForm = null)
    {
        $this->applicationForm = $applicationForm;

        return $this;
    }

    /**
     * Get applicationForm
     *
     * @return ApplicationForm
     */
    public function getApplicationForm()
    {
        return $this->applicationForm;
    }

    /**
     * Add applicationField
     *
     * @param ApplicationField $applicationField
     *
     * @return ApplicationSection
     */
    public function addApplicationField(ApplicationField $applicationField)
    {
        $this->applicationFields[] = $applicationField;

        return $this;
    }

    /**
     * Remove applicationField
     *
     * @param ApplicationField $applicationField
     */
    public function removeApplicationField(ApplicationField $applicationField)
    {
        $this->applicationFields->removeElement($applicationField);
    }

    /**
     * Get applicationFields
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplicationFields()
    {
        return $this->applicationFields;
    }
}
