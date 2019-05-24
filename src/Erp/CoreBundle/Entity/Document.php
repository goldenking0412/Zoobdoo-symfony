<?php

namespace Erp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Document
 *
 * @ORM\Table(name="documents")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Document {

    const SIZE_IN_KB = 'kb';
    const SIZE_IN_MB = 'mb';

    /**
     * @var string
     */
    public static $patternFilename = '/^(.+)\.([\w]{1,4})+$/';

    /**
     * @var string
     */
    public static $messagePatternFilename = 'The file name must not be empty and must contain an extension';

    /**
     * @var int
     */
    public static $maxSize = 2097152; // 2 * 1024 * 1024

    /**
     * @var string
     */
    public static $maxSizeMessage = 'The file is too large. Allowed maximum size is {maxSize} {sizeIn}';

    /**
     * @var array
     */
    public static $mimeTypes = [
        'application/pdf',
        'application/x-pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword',
        'image/png',
        'image/jpeg',
        'image/pjpeg',
        'image/gif',
        'image/tiff'
    ];

    /**
     * @var string
     */
    public static $mimeTypesMessage = 'Wrong file format. Allowed file types is .png, .jpg, .gif, .tif, .pdf, .doc, .docx';

    /**
     * @var string
     */
    public static $commonMessage = 'Some of the files you added were not uploaded because of invalid type or size larger {maxSize} {sizeIn}';

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
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="original_name", type="string", length=255)
     *
     * @Assert\NotBlank(groups={"EditProperty"})
     */
    protected $originalName;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
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
     *
     * @Assert\File(
     *     mimeTypes={
     *          "application/pdf",
     *          "application/x-pdf",
     *          "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
     *          "application/msword",
     *          "image/png",
     *          "image/jpeg",
     *          "image/pjpeg",
     *          "image/gif",
     *          "image/tiff"
     *     },
     *     groups={"EditProperty","Remittances"}
     * )
     */
    private $file;

    /**
     * @var File
     */
    private $temp;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Document
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set original name
     *
     * @param string $originalName
     *
     * @return Document
     */
    public function setOriginalName($originalName) {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Get original name
     *
     * @return string
     */
    public function getOriginalName() {
        return $this->originalName;
    }

    /**
     * Set createdDate
     *
     * @ORM\PrePersist
     */
    public function setCreatedDate() {
        $this->createdDate = new \DateTime();
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate() {
        return $this->createdDate;
    }

    /**
     * Set updatedDate
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedDate() {
        $this->updatedDate = new \DateTime();
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate() {
        return $this->updatedDate;
    }

    /**
     * @return string
     */
    public function getAbsolutePath() {
        return $this->getUploadRootDir() . '/' . $this->getName();
    }

    /**
     * @return string
     */
    public function getWebPath() {
        return $this->getUploadDir() . '/' . $this->getName();
    }

    /**
     * @return string
     */
    public function getUploadRootDir() {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . $this->rootPath . $this->getUploadDir();
    }

    /**
     * @return string
     */
    public function getUploadDir() {
        $dir = 'uploads/documents/' . md5(date('m-Y'));

        if (!is_dir(__DIR__ . $this->rootPath . $dir)) {
            var_dump(__DIR__ . $this->rootPath . $dir);
            
            mkdir(__DIR__ . $this->rootPath . $dir, 0755, true);
        }

        return $dir;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if ($this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->setName($filename . '.' . $this->getFile()->guessExtension());
            $this->setPath($this->getUploadDir());

            $fileOriginalName = $this->getFile()->getClientOriginalName();
            $originalName = (!$this->getOriginalName()) ? $fileOriginalName : $this->getOriginalName();

            $this->setOriginalName($originalName);
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->name);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir() . '/' . $this->temp);
            // clear the temp image path
            $this->temp = null;
        }

        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
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
    public function setFile(UploadedFile $file = null) {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Document
     */
    public function setPath($path) {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return string
     */
    public function getUploadBaseDir(ContainerInterface $container) {
        return $container->get('kernel')->getRootDir() . '/../web/';
    }

    /**
     * Get file extension
     *
     * @return mixed
     */
    public function getExtension() {
        $pathParts = pathinfo($this->getPath() . '/' . $this->getName());

        return $pathParts['extension'];
    }

    /**
     * Get file size
     *
     * @param bool $withString
     *
     * @return float|string
     */
    public function getFileSize($withString = false) {
        $filesize = filesize($this->getPath() . '/' . $this->getName());
        $mb = false;
        $result = round($filesize / 1000, 1);

        if ($result > 1024) {
            $result = round($filesize / (1000 * 1000), 1);
            $mb = true;
        }

        if ($withString) {
            $string = $mb ? ' mb' : ' kb';
            $result .= $string;
        }

        return $result;
    }

}
