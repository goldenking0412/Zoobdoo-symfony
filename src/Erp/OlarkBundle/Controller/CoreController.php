<?php

namespace Erp\OlarkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller
{
    public function chatWindowAction(Request $request)
    {
        return $this->render(
        	'ErpOlarkBundle:Core:chat_window.html.twig'
        );
    }
}
