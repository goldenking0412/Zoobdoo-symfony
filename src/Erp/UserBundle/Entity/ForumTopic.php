<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Erp\UserBundle\Entity\ForumComment;
use Erp\UserBundle\Entity\User;

/**
 * ForumTopic
 *
 * @ORM\Table(name="forum_topics")
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\ForumTopicRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ForumTopic
{
    const LIMIT_FORUM_TOPICS = 5;
    const LIMIT_TOP_FORUM_TOPICS = 5;

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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     *
     * @Assert\NotBlank(
     *     message="Please enter title",
     *     groups={"CreatedForumTopic"}
     * )
     * @Assert\Length(
     *     min=2,
     *     max="255",
     *     minMessage="Title should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Title code should have minimum 2 characters and maximum 255 characters",
     *     groups={"CreatedForumTopic"}
     * )
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     *
     * @Assert\NotBlank(
     *     message="Please enter text",
     *     groups={"CreatedForumTopic"}
     * )
     * @Assert\Length(
     *     min=1,
     *     max="1000",
     *     minMessage="Text should have minimum 1 characters and maximum 1000 characters",
     *     maxMessage="Text code should have minimum 1 characters and maximum 1000 characters",
     *     groups={"CreatedForumTopic"}
     * )
     */
    protected $text;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\UserBundle\Entity\User",
     *      inversedBy="forumTopics"
     * )
     * @ORM\JoinColumn(
     *      name="user_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $user;

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
     *      targetEntity="\Erp\UserBundle\Entity\ForumComment",
     *      mappedBy="forumTopic"
     * )
     * @ORM\OrderBy({"createdDate"="DESC"})
     */
    protected $forumComments;

    /**
     * Consructor
     */
    public function __construct()
    {
        $this->forumComments = new ArrayCollection();
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
     * @return ForumTopic
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
     * Set text
     *
     * @param string $text
     *
     * @return ForumTopic
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set createdDate
     *
     * @ORM\PrePersist
     *
     * @return $this
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
     * @return $this
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
     * Set user
     *
     * @param User $user
     *
     * @return ForumTopic
     */
    public function setUser(User $user = null)
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
     * Add forumComment
     *
     * @param ForumComment $forumComment
     *
     * @return ForumTopic
     */
    public function addForumComment(ForumComment $forumComment)
    {
        $this->forumComments[] = $forumComment;

        return $this;
    }

    /**
     * Remove forumComment
     *
     * @param ForumComment $forumComment
     */
    public function removeForumComment(ForumComment $forumComment)
    {
        $this->forumComments->removeElement($forumComment);
    }

    /**
     * Get forumComments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getForumComments()
    {
        return $this->forumComments;
    }
}
