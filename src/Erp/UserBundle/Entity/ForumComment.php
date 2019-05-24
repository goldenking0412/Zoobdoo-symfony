<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Erp\UserBundle\Entity\ForumTopic;
use Erp\UserBundle\Entity\User;

/**
 * ForumComment
 *
 * @ORM\Table(name="forum_comments")
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\ForumCommentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ForumComment
{
    const LIMIT_FORUM_COMMENTS = 5;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\UserBundle\Entity\User"
     * )
     * @ORM\JoinColumn(
     *      name="user_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $user;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\UserBundle\Entity\ForumTopic",
     *      inversedBy="forumComments"
     * )
     * @ORM\JoinColumn(
     *      name="topic_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $forumTopic;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     *
     * @Assert\NotBlank(
     *     message="Please enter text",
     *     groups={"CreatedForumComment"}
     * )
     * @Assert\Length(
     *     min=1,
     *     max="1000",
     *     minMessage="Text should have minimum 1 characters and maximum 1000 characters",
     *     maxMessage="Text code should have minimum 1 characters and maximum 1000 characters",
     *     groups={"CreatedForumComment"}
     * )
     */
    private $text;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return ForumComment
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
     * Set forumTopic
     *
     * @param ForumTopic $forumTopic
     *
     * @return ForumComment
     */
    public function setForumTopic(ForumTopic $forumTopic)
    {
        $this->forumTopic = $forumTopic;

        return $this;
    }

    /**
     * Get forumTopic
     *
     * @return ForumTopic
     */
    public function getForumTopic()
    {
        return $this->forumTopic;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return ForumComment
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
}
