<?php
namespace Erp\CoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Logger
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $path;

    /**
     * Construct method
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->path = $this->container->get('kernel')->getRootDir() . '/logs/';
    }

    /**
     * Add line in log file by type
     *
     * @param string $type
     * @param string $message
     * @param string $prefix
     *
     * @return $this
     */
    public function add($type, $message, $prefix = 'INFO')
    {
        if (!is_dir($this->path . $type)) {
            mkdir($this->path . $type, 0755, true);
        }

        $datetime = new \DateTime();
        $fileName = $type . '_' . $datetime->format('Ymd') . '.log';
        $file = fopen($this->path . $type . '/' . $fileName, 'a+');
        $message = '[' . $datetime->format('Y-m-d H:i:s') . '] ' . $prefix . ':' . $message . "\n";
        fwrite($file, $message);
        fclose($file);

        return $this;
    }
}
