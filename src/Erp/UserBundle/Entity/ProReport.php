<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\UserBundle\Entity\ProConsultant;

/**
 * ProReport
 *
 * @ORM\Table(name="pro_report")
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\ProReportRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ProReport
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\ProConsultant", inversedBy="proReports")
     * @ORM\JoinColumn(name="pro_consultant_id", referencedColumnName="id", nullable=false)
     */
    protected $proConsultant;

    /**
     * @var int
     *
     * @ORM\Column(name="count_users", type="integer")
     */
    private $countUsers;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime")
     */
    private $updatedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approved_date", type="datetime")
     */
    private $approvedDate;


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
     * Set proConsultant
     *
     * @param ProConsultant $proConsultant
     *
     * @return $this
     */
    public function setProConsultant(ProConsultant $proConsultant)
    {
        $this->proConsultant = $proConsultant;

        return $this;
    }

    /**
     * Get proConsultant
     *
     * @return ProConsultant
     */
    public function getProConsultant()
    {
        return $this->proConsultant;
    }

    /**
     * Set countUsers
     *
     * @param int $countUsers
     *
     * @return ProReport
     */
    public function setCountUsers($countUsers)
    {
        $this->countUsers = $countUsers;

        return $this;
    }

    /**
     * Get countUsers
     *
     * @return \int
     */
    public function getCountUsers()
    {
        return $this->countUsers;
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
     * Set approvedDate
     *
     * @param \DateTime $approvedDate
     *
     * @return ProReport
     */
    public function setApprovedDate($approvedDate)
    {
        $this->approvedDate = $approvedDate;

        return $this;
    }

    /**
     * Get approvedDate
     *
     * @return \DateTime
     */
    public function getApprovedDate()
    {
        return $this->approvedDate;
    }
}
