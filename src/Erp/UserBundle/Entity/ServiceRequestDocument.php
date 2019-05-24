<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Erp\UserBundle\Entity\User;

/**
 * ServiceRequest
 *
 * @ORM\Table(name="service_requests_documents")
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\ServiceRequestRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ServiceRequestDocument
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */

    protected $name;


    /**
     * @var string
     *
     * @ORM\Column(name="original_name", type="string", length=255, nullable=true)
     */

    protected $originalName;


    /**
     * @var ServiceRequest
     *
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\ServiceRequest", inversedBy="documents")
     * @ORM\JoinColumn(name="service_request_id", referencedColumnName="id")
     */
    protected $serviceRequest;

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
     * @return Message
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set originalName
     *
     * @param string $originalName
     *
     * @return Message
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Set serviceRequest
     *
     * @param ServiceRequest $serviceRequest
     *
     * @return ServiceRequestDocument
     */
    public function setServiceRequest(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;

        return $this;
    }

    /**
     * Get serviceRequest
     *
     * @return ServiceRequest
     */
    public function getServiceRequest()
    {
        return $this->serviceRequest;
    }

}
