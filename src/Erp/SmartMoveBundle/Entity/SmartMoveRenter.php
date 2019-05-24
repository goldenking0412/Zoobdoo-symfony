<?php

namespace Erp\SmartMoveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\UserBundle\Entity\User;

/**
 * SmartMoveRenter
 *
 * @ORM\Table(name="sm_renters")
 * @ORM\Entity(repositoryClass="Erp\SmartMoveBundle\Repository\SmartMoveRenterRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class SmartMoveRenter
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\User", inversedBy="smartMoveRenters")
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id", nullable=false)
     */
    protected $manager;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;

    /**
     * @var int
     *
     * @ORM\Column(name="sm_property_id", type="integer", nullable=true)
     */
    protected $smPropertyId;

    /**
     * @var int
     *
     * @ORM\Column(name="sm_application_id", type="integer", nullable=true)
     */
    protected $smApplicationId;

    /**
     * @var string
     *
     * @ORM\Column(name="exams", type="text", nullable=true)
     */
    protected $exams;

    /**
     * @var string
     *
     * @ORM\Column(name="reports", type="text", nullable=true)
     */
    protected $reports;

    /**
     * @var string
     *
     * @ORM\Column(name="personal_token", type="text")
     */
    protected $personalToken;

    /**
     * @var string
     *
     * @ORM\Column(name="exam_token", type="text", nullable=true)
     */
    protected $examToken;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_personal_completed", type="boolean")
     */
    protected $isPersonalComleted = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_exam_completed", type="boolean")
     */
    protected $isExamComleted = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_added_as_applicant", type="boolean")
     */
    protected $isAddedAsApplicant = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_accepted", type="boolean")
     */
    protected $isAccepted = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_payed", type="boolean")
     */
    protected $isPayed = false;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=true)
     */
    protected $info;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tenant
     *
     * @param User $manager
     *
     * @return $this
     */
    public function setManager(User $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get tenant
     *
     * @return User
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set smPropertyId
     *
     * @param int $smPropertyId
     *
     * @return $this
     */
    public function setSmPropertyId($smPropertyId)
    {
        $this->smPropertyId = $smPropertyId;

        return $this;
    }

    /**
     * Get smPropertyId
     *
     * @return int
     */
    public function getSmPropertyId()
    {
        return $this->smPropertyId;
    }

    /**
     * Set smApplicationId
     *
     * @param int $smApplicationId
     *
     * @return $this
     */
    public function setSmApplicationId($smApplicationId)
    {
        $this->smApplicationId = $smApplicationId;

        return $this;
    }

    /**
     * Get smApplicationId
     *
     * @return int
     */
    public function getSmApplicationId()
    {
        return $this->smApplicationId;
    }

    /**
     * Set exams
     *
     * @param string $exams
     *
     * @return $this
     */
    public function setExams($exams)
    {
        $this->exams = $exams;

        return $this;
    }

    /**
     * Get exams
     *
     * @return string
     */
    public function getExams()
    {
        return $this->exams;
    }

    /**
     * Set reports
     *
     * @param string $reports
     *
     * @return $this
     */
    public function setReports($reports)
    {
        $this->reports = $reports;

        return $this;
    }

    /**
     * Get reports
     *
     * @return string
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * Set personalToken
     *
     * @param string $personalToken
     *
     * @return $this
     */
    public function setPersonalToken($personalToken)
    {
        $this->personalToken = $personalToken;

        return $this;
    }

    /**
     * Get personalToken
     *
     * @return string
     */
    public function getPersonalToken()
    {
        return $this->personalToken;
    }

    /**
     * Set examToken
     *
     * @param string $examToken
     *
     * @return $this
     */
    public function setExamToken($examToken)
    {
        $this->examToken = $examToken;

        return $this;
    }

    /**
     * Get examToken
     *
     * @return string
     */
    public function getExamToken()
    {
        return $this->examToken;
    }

    /**
     * Set isPersonalComleted
     *
     * @param boolean $isPersonalComleted
     *
     * @return $this
     */
    public function setIsPersonalComleted($isPersonalComleted)
    {
        $this->isPersonalComleted = $isPersonalComleted;

        return $this;
    }

    /**
     * Get isPersonalComleted
     *
     * @return boolean
     */
    public function getIsPersonalComleted()
    {
        return $this->isPersonalComleted;
    }

    /**
     * Set isExamComleted
     *
     * @param boolean $isExamComleted
     *
     * @return $this
     */
    public function setIsExamComleted($isExamComleted)
    {
        $this->isExamComleted = $isExamComleted;

        return $this;
    }

    /**
     * Get isExamComleted
     *
     * @return boolean
     */
    public function getIsExamComleted()
    {
        return $this->isExamComleted;
    }

    /**
     * Set isAddedAsApplicant
     *
     * @param boolean $isAddedAsApplicant
     *
     * @return $this
     */
    public function setIsAddedAsApplicant($isAddedAsApplicant)
    {
        $this->isAddedAsApplicant = $isAddedAsApplicant;

        return $this;
    }

    /**
     * Get isAddedAsApplicant
     *
     * @return boolean
     */
    public function getIsAddedAsApplicant()
    {
        return $this->isAddedAsApplicant;
    }

    /**
     * Set isAccepted
     *
     * @param boolean $isAccepted
     *
     * @return $this
     */
    public function setIsAccepted($isAccepted)
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }

    /**
     * Get isAccepted
     *
     * @return boolean
     */
    public function getIsAccepted()
    {
        return $this->isAccepted;
    }

    /**
     * Set isPayed
     *
     * @param boolean $isPayed
     *
     * @return $this
     */
    public function setIsPayed($isPayed)
    {
        $this->isPayed = $isPayed;

        return $this;
    }

    /**
     * Get isPayed
     *
     * @return boolean
     */
    public function getIsPayed()
    {
        return $this->isPayed;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return $this
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
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
}
