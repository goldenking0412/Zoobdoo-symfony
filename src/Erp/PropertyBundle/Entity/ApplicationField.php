<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\ApplicationSection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ApplicationField
 *
 * @ORM\Table(name="application_fields")
 * @ORM\Entity(repositoryClass="Erp\PropertyBundle\Repository\ApplicationFieldRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ApplicationField
{
    const TYPE_FILE = 'file';
    const TYPE_TEXT = 'text';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO = 'radio';

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
     * @ORM\Column(
     *     name="type",
     *     length=16,
     *     type="string",
     *     nullable=true
     * )
     *
     * @Assert\NotBlank(
     *      message="The field 'type' can not be empty",
     *      groups={"ApplicationField", "ApplicationFieldType"}
     * )
     */
    protected $type = self::TYPE_TEXT;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(
     *      message="The field can not be empty", groups={"ApplicationField"}
     * )
     *
     * @Assert\Length(
     *     min=1,
     *     max=255,
     *     minMessage="Name should have minimum 1 characters and maximum 255 characters",
     *     maxMessage="Name should have minimum 1 characters and maximum 255 characters",
     *     groups={"ApplicationField"}
     * )
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    protected $data;

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
     *      targetEntity="\Erp\PropertyBundle\Entity\ApplicationSection",
     *      inversedBy="applicationFields"
     * )
     * @ORM\JoinColumn(
     *      name="application_section_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $applicationSection;

    /**
     * Clone
     */
    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
        }
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return $this
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
     * Set type
     *
     * @param string $type
     *
     * @return ApplicationField
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ApplicationField
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
     * Set data
     *
     * @param string $data
     *
     * @return ApplicationField
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     *
     * @return ApplicationField
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
     * @return AppointmentRequest
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
     * Set applicationSection
     *
     * @param ApplicationSection $applicationSection
     *
     * @return ApplicationField
     */
    public function setApplicationSection(ApplicationSection $applicationSection = null)
    {
        $this->applicationSection = $applicationSection;

        return $this;
    }

    /**
     * Get applicationSection
     *
     * @return ApplicationSection
     */
    public function getApplicationSection()
    {
        return $this->applicationSection;
    }
}
