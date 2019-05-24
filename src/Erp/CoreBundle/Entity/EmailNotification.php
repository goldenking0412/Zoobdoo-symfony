<?php

namespace Erp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EmailNotification
 *
 * @ORM\Table(name="email_notifications")
 * @ORM\Entity(repositoryClass="Erp\CoreBundle\Repository\EmailNotificationRepository")
 */
class EmailNotification
{
    const SETTING_SERVICE_REQUESTS = 'SERVICE_REQUESTS';
    const SETTING_FORUM_TOPICS = 'FORUM_TOPICS';
    const SETTING_PROFILE_MESSAGES = 'PROFILE_MESSAGES';

    const EVENT_SEND_EMAIL_NOTIFICATION = 'send.email_notification';

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
     * @ORM\Column(name="type", type="string", length=255)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="button", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Please enter button text",
     *     groups={"EmailNotification"}
     * )
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Button text should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Button text should have minimum 2 characters and maximum 255 characters",
     *     groups={"EmailNotification"}
     * )
     */
    protected $button;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Please enter subject",
     *     groups={"EmailNotification"}
     * )
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Subject should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Subject should have minimum 2 characters and maximum 255 characters",
     *     groups={"EmailNotification"}
     * )
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Please enter title",
     *     groups={"EmailNotification"}
     * )
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Title should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Title should have minimum 2 characters and maximum 255 characters",
     *     groups={"EmailNotification"}
     * )
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     *
     * @Assert\Length(
     *     min=2,
     *     max=1000,
     *     minMessage="Body should have minimum 2 characters and maximum 1000 characters",
     *     maxMessage="Body should have minimum 2 characters and maximum 1000 characters",
     *     groups={"EmailNotification"}
     * )
     */
    protected $body;

    /**
     * @var string
     *
     * @ORM\Column(name="tokens", type="text")
     */
    protected $tokens;

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
     * @return EmailNotification
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
     * @return EmailNotification
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
     * Set button
     *
     * @param string $button
     *
     * @return EmailNotification
     */
    public function setButton($button)
    {
        $this->button = $button;

        return $this;
    }

    /**
     * Get button
     *
     * @return string
     */
    public function getButton()
    {
        return $this->button;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return EmailNotification
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
     * Set title
     *
     * @param string $title
     *
     * @return EmailNotification
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return EmailNotification
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set tokens
     *
     * @param string $tokens
     *
     * @return EmailNotification
     */
    public function setTokens($tokens)
    {
        $this->tokens = json_encode($tokens, JSON_FORCE_OBJECT);

        return $this;
    }

    /**
     * Get tokens
     *
     * @return string
     */
    public function getTokens()
    {
        return json_decode($this->tokens, true);
    }
}
