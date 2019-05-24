<?php

namespace Erp\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

abstract class BaseController extends SymfonyController {

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $ajaxResponse = null;

    /**
     * This used instead of __construct as Symfony2 controllers don't support constructors by default
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->em = $this->getDoctrine()->getManager();
        $this->request = $this->getRequest();
    }

    /**
     * Rewrite render method
     *
     * @param string     $view
     * @param array    $parameters
     * @param Response $response
     *
     * @return Response
     */
    public function render($view, array $parameters = [], Response $response = null) {
        $response = parent::render($view, $parameters, $response);
        if ($this->request && $this->request->isXmlHttpRequest()) {
            $this->ajaxResponse = $response->getContent();
            $response->setContent($this->ajaxResponse);
        }

        return $response;
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url    The URL to redirect to
     * @param int    $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302) {
        if ($this->request && $this->request->isXmlHttpRequest()) {
            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(
                    array(
                        'redirect' => $url,
                    )
            );

            return $response;
        }

        return new RedirectResponse($url, $status);
    }

    /**
     * 
     * @param string $pdf
     * @param string $fileName
     * @return PdfResponse
     */
    public function pdfResponse($pdf, $fileName) {
        return new PdfResponse($pdf, $fileName);
    }

}
