<?php

namespace Erp\CoreBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CoreExtension
 *
 * @package Erp\UserBundle\Twig
 */
class CoreExtension extends \Twig_Extension {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'core_extension';
    }

    /**
     * @return array
     */
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('json_decode', array($this, 'jsonDecode')),
            new \Twig_SimpleFilter('money', array($this, 'formatMoney')),
            new \Twig_SimpleFilter('internal_type', array($this, 'formatInternalType')),
            new \Twig_SimpleFilter('transaction_status', array($this, 'formatTransactionStatus')),
            new \Twig_SimpleFilter('list_functions', 'get_class_methods'),
        );
    }

    public function getTests() {
        return array(
            new \Twig_SimpleTest('instanceof', array($this, 'isInstanceof')),
        );
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('get_class', 'get_class'),
        );
    }

    /**
     * Decode string to array
     *
     * @param $str
     *
     * @return array
     */
    public function jsonDecode($str) {
        return json_decode($str, true);
    }

    public function formatMoney($value) {
        if (null === $value || '' === $value) {
            return $value;
        }

        $formatter = $this->container->get('erp_core.formatter.money_formatter');

        return $formatter->format($value);
    }

    public function formatInternalType($value) {
        $formatter = $this->container->get('erp_core.formatter.internal_type_formatter');

        return $formatter->format($value);
    }

    public function formatTransactionStatus($value) {
        $formatter = $this->container->get('erp_core.formatter.transaction_status_formatter');

        return $formatter->format($value);
    }
    
    /**
     * @param $var
     * @param $instance
     * @return bool
     */
    public function isInstanceof($var, $instance) {
        return $var instanceof $instance;
    }

}
