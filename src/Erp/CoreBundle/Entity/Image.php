<?php

namespace Erp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Image
 *
 * @ORM\Table(name="images")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Image
{
    /**
     * @var int
     */
    public static $maxSize = 5242880; // 5 * 1024 * 1024

    /**
     * @var string
     */
    public static $maxSizeMessage = 'The file is too large. Allowed maximum size is {maxSize} {sizeIn}';

    /**
     * @var array
     */
    public static $allowedMimeTypes = [
        'image/png',
        'image/jpeg',
        'image/pjpeg',
        'image/gif'
    ];

    /**
     * @var string
     */
    public static $mimeTypesMessage = 'Wrong file format. Valid types are: PNG, JPG, GIF';

    /**
     * @var string
     */
    public static $commonMessage =
        'Some of the files you added were not uploaded because of invalid type or size larger {maxSize} {sizeIn}';

    /**
     * @var string
     */
    public $rootPath = '/../../../../web/';

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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     * @Assert\NotBlank
     */
    protected $path;

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
     * @var File
     */
    private $image;

    /**
     * @var File
     */
    private $temp;
    
    public function __toString() {
        return $this->path . DIRECTORY_SEPARATOR . $this->name;
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
     * @return Image
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
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->getUploadRootDir() . '/' . $this->getName();
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        return $this->getUploadDir() . '/' . $this->getName();
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . $this->rootPath . $this->getUploadDir();
    }

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        $dir = 'uploads/images/' . md5(date('m-Y'));

        if (!is_dir(__DIR__ . $this->rootPath . $dir)) {
            mkdir(__DIR__ . $this->rootPath . $dir, 0755, true);
        }

        return $dir;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @ORM\PreFlush()
     */
    public function preUpload()
    {
        if (null !== $this->getImage()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->name = $filename . '.' . $this->getImage()->guessExtension();
            $this->path = $this->getUploadDir();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getImage()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getImage()->move($this->getUploadRootDir(), $this->name);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir() . '/' . $this->temp);
            // clear the temp image path
            $this->temp = null;
        }

        $this->image = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if (file_exists($file)) {
            unlink($file);

            if (is_dir($this->getUploadRootDir()) && count(scandir($this->getUploadRootDir())) === 2) {
                rmdir($this->getUploadRootDir());
            }
        }
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setImage(UploadedFile $file = null)
    {
        $this->image = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Image
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
