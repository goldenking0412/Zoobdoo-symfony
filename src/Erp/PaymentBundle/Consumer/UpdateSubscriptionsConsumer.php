<?php

namespace Erp\PaymentBundle\Consumer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Erp\StripeBundle\Entity\ApiManager;
use Erp\PaymentBundle\Entity\StripeSubscription;
use Erp\PaymentBundle\Entity\UnitSettings;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Stripe\Subscription;

class UpdateSubscriptionsConsumer implements ConsumerInterface
{
    /**
     * @var ApiManager
     */
    private $manager;

    /**
     * @var ManagerRegistry
     */
    private $registry;

    public function __construct(ManagerRegistry $registry, ApiManager $manager)
    {
        $this->registry = $registry;
        $this->manager = $manager;
    }

    /**
     * @inheritdoc
     */
    public function execute(AMQPMessage $msg)
    {
        $body = $msg->getBody();
        $object = unserialize($body);

        //TODO Handle settings
    }

    private function getRepository()
    {
        return $this->registry->getManagerForClass(StripeSubscription::class)->getRepository(StripeSubscription::class);
    }
}