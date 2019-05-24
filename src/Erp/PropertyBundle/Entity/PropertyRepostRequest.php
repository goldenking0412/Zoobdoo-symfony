<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\Property;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PropertyRepostRequest
 *
 * @ORM\Table(name="property_repost_requests")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class PropertyRepostRequest
{
    const STATUS_NEW = 'new';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

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
     *      name="status",
     *      length=16,
     *      type="string",
     *      nullable=true
     * )
     */
    protected $status = self::STATUS_NEW;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255, nullable=true)
     *
     * @Assert\Length(
     *     max="255",
     *     maxMessage="Note should have minimum 2 characters and maximum 255 characters",
     *     groups={"PropertyRepostRequest"}
     * )
     */
    protected $note;

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
     *      targetEntity="\Erp\PropertyBundle\Entity\Property",
     *      inversedBy="propertyRepostRequests"
     * )
     * @ORM\JoinColumn(
     *      name="property_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $property;

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
     * Set status
     *
     * @param string $status
     *
     * @return PropertyRepostRequest
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
     * @return PropertyRepostRequest
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
     * Set note
     *
     * @param string $note
     *
     * @return PropertyRepostRequest
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }
}
