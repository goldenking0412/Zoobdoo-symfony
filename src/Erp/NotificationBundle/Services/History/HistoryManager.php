<?php

namespace Erp\NotificationBundle\Services\History;

use Erp\NotificationBundle\Entity\History;
use Erp\NotificationBundle\Repository\HistoryRepository;
use Erp\UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HistoryManager
{
    private $repo;

    public function __construct(HistoryRepository $repo)
    {
        $this->repo = $repo;
    }

    public function get(int $id) : History
    {
        if ($history = $this->repo->find($id)) {
            return $history;
        }
        throw new NotFoundHttpException('Alert history with id '.$id.' not found');
    }

    public function create(array $fields = []) : History
    {
        $fields = $this->prepare($fields);
        $history = History::createFromArray($fields);
        return $history;
    }

    public function markAsReceived(History $history) : History
    {
        $history->markAsReceived();
        return $history;
    }

    private function prepare(array $fields = []) : array
    {
        $fields = $this->prepareTenant($fields);
        $fields = $this->prepareReceivedAt($fields);
        return $fields;
    }

    private function prepareTenant(array $fields = []) : array
    {
        if (isset($fields['tenant']) && $fields['tenant'] && $fields['tenant'] instanceof User) {
            $fields['tenantName'] = $fields['tenant']->getFullName();
        }
        return $fields;
    }

    private function prepareReceivedAt(array $fields = []) : array
    {
        if (isset($fields['receivedAt']) && $fields['receivedAt'] && !$fields['receivedAt'] instanceof \DateTime) {
            $fields['receivedAt'] = new \DateTime($fields['receivedAt']);
        }
        return $fields;
    }
}
