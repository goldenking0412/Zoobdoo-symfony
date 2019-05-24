<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\ProConsultant;

/**
 * ProRequest
 *
 * @ORM\Table(name="pro_requests")
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\ProRequestRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ProRequest
{
    const STATUS_IN_PROCESS    = 'in_process';
    const STATUS_APPROVED      = 'approved';
    const STATUS_PAYMENT_OK    = 'payment_ok';
    const STATUS_PAYMENT_ERROR = 'payment_error';
    const STATUS_CANCELED      = 'canceled';

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
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\User", inversedBy="proRequests")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\ProConsultant", inversedBy="proRequests")
     * @ORM\JoinColumn(name="pro_consultant_id", referencedColumnName="id")
     */
    protected $proConsultant;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter Subject", groups={"ProRequestCreate"})
     *
     * @Assert\Length(
     *     min=1,
     *     max=255,
     *     minMessage="Subject should have minimum 1 characters and maximum 255 characters",
     *     maxMessage="Subject should have minimum 1 characters and maximum 255 characters",
     *     groups={"ProRequestCreate"}
     * )
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     *
     * @Assert\NotBlank(message="Please enter Message", groups={"ProRequestCreate"})
     *
     * @Assert\Length(
     *     min=1,
     *     max=1000,
     *     minMessage="Message should have minimum 1 characters and maximum 1000 characters",
     *     maxMessage="Message should have minimum 1 characters and maximum 1000 characters",
     *     groups={"ProRequestCreate"}
     * )
     */
    protected $message;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_refferal", type="boolean")
     */
    protected $isRefferal = false;

    /**
     * @var float
     *
     * @ORM\Column(name="consultant_fee", type="float", nullable=true)
     *
     * @Assert\Range(
     *      min = 0.01,
     *      max = 1000000,
     *      minMessage = "Min price $0.01, Max - $1 000 000",
     *      maxMessage = "Min price $0.01, Max - $1 000 000",
     *      groups={"ProRequestAdmin"}
     * )
     *
     * @Assert\NotBlank(
     *      message=" Please enter Consultant Fee",
     *      groups={"ProRequestAdmin"}
     * )
     *
     * @Assert\Type(
     *      type="float",
     *      message="Something isn’t right here, check your field",
     *      groups={"ProRequestAdmin"}
     * )
     */
    protected $consultantFee;

    /**
     * @var float
     *
     * @ORM\Column(name="servicing_fee", type="float")
     *
     * @Assert\NotBlank(message="Please enter servicing Fee", groups={"ProRequestAdmin"})
     */
    protected $servicingFee;

    /**
     * @var string
     *
     * @ORM\Column(
     *      name="status",
     *      length=32,
     *      type="string",
     *      nullable=true
     * )
     */
    protected $status = self::STATUS_IN_PROCESS;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approved_date", type="datetime", nullable=true)
     */
    protected $approvedDate;

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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set proConsultant
     *
     * @param ProConsultant $proConsultant
     *
     * @return $this
     */
    public function setProConsultant(ProConsultant $proConsultant = null)
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
     * Set subject
     *
     * @param string $subject
     *
     * @return ProRequest
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return ProRequest
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set isReveral
     *
     * @param boolean $isRefferal
     *
     * @return ProRequest
     */
    public function setIsRefferal($isRefferal)
    {
        $this->isRefferal = $isRefferal;

        return $this;
    }

    /**
     * Get isReveral
     *
     * @return boolean
     */
    public function getIsRefferal()
    {
        return $this->isRefferal;
    }

    /**
     * Set consultantFee
     *
     * @param float $consultantFee
     *
     * @return ProRequest
     */
    public function setConsultantFee($consultantFee)
    {
        $this->consultantFee = $consultantFee;

        return $this;
    }

    /**
     * Get consultantFee
     *
     * @return float
     */
    public function getConsultantFee()
    {
        return $this->consultantFee;
    }

    /**
     * Set servicingFee
     *
     * @param float $servicingFee
     *
     * @return ProRequest
     */
    public function setServicingFee($servicingFee)
    {
        $this->servicingFee = $servicingFee;

        return $this;
    }

    /**
     * Get servicingFee
     *
     * @return float
     */
    public function getServicingFee()
    {
        return $this->servicingFee;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return ProRequest
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
     * Set approvedDate
     *
     * @param \DateTime $approvedDate
     *
     * @return ProRequest
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
