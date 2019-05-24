<?php

namespace Erp\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ErpUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
