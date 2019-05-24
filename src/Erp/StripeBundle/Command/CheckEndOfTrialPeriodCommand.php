<?php

namespace Erp\StripeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PaymentBundle\Entity\StripeSubscription;

class CheckEndOfTrialPeriodCommand extends ContainerAwareCommand {

    /**
     * @inheritdoc
     */
    protected function configure() {
        $this
                ->setName('erp:stripe:subscription:check-end-of-trial-period')
                ->setDescription('Check subscription\'s end of trial period');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $now = new \DateTime();
        $afterThreeDays = $now->modify('+3 days');

        $repository = $this->getContainer()->get('doctrine')->getManagerForClass(StripeSubscription::class)->getRepository(StripeSubscription::class);
        $subscriptions = $repository->getSubscriptionsByEndOfTrialPeriod($afterThreeDays);

        $processor = $this->getContainer()->get('erp_user.mailer.processor');

        /** @var StripeSubscription $subscription */
        foreach ($subscriptions as $subscription) {
            $user = $subscription->getStripeCustomer()->getUser();

            $processor->sendEndOfTrialPeriodEmail($user);
        }
    }

}
