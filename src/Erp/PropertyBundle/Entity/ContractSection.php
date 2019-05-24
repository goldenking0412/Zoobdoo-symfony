<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\ContractForm;

/**
 * ContractSection
 *
 * @ORM\Table(name="contract_sections")
 * @ORM\Entity(repositoryClass="Erp\PropertyBundle\Repository\ContractSectionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ContractSection
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
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer")
     */
    protected $sort;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\PropertyBundle\Entity\ContractForm",
     *      inversedBy="contractSections"
     * )
     * @ORM\JoinColumn(
     *      name="form_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $contractForm;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected $content;

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
     * Set sort
     *
     * @param integer $sort
     *
     * @return ContractSection
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
     * Set content
     *
     * @param string $content
     *
     * @return ContractSection
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
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
     * Set contractForm
     *
     * @param ContractForm $contractForm
     *
     * @return ContractSection
     */
    public function setContractForm(ContractForm $contractForm = null)
    {
        $this->contractForm = $contractForm;

        return $this;
    }

    /**
     * Get contractForm
     *
     * @return ContractForm
     */
    public function getContractForm()
    {
        return $this->contractForm;
    }
}
