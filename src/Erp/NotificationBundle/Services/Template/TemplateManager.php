<?php

namespace Erp\NotificationBundle\Services\Template;

use Erp\NotificationBundle\Entity\Template;
use Erp\NotificationBundle\Repository\TemplateRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\TwigBundle\TwigEngine;

class TemplateManager {

    const EMAIL_TEMPLATE = 'ErpNotificationBundle:Template:mail.html.twig';

    private $twig;
    private $repo;

    public function __construct(TwigEngine $twig, TemplateRepository $repo) {
        $this->twig = $twig;
        $this->repo = $repo;
    }

    public function renderTemplateById($id) {
        if ($template = $this->repo->find($id)) {
            return $this->renderTemplate($template);
        }
        throw new NotFoundHttpException('Template with id ' . $id . ' not found');
    }

    public function renderTemplate(Template $template, array $parameters = []) {
        $rendered = (new \Twig_Environment(new \Twig_Loader_String()))->render($template->getDescription(), $parameters);

        return $this->twig->render(self::EMAIL_TEMPLATE, ['content' => $rendered]);
    }

    public function getTemplate($id) {
        $template = $this->repo->find($id);

        if (!$template) {
            throw new NotFoundHttpException('Template with id ' . $id . ' not found');
        }

        return $template;
    }

}
