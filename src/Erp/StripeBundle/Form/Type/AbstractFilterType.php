<?php


namespace Erp\StripeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

abstract class AbstractFilterType extends AbstractType
{
    const NAME = 'filter';

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::NAME;
    }
}
