<?php

namespace Erp\StripeBundle\Form\Type;

use Erp\PaymentBundle\Plaid\Service\Item;
use Erp\PaymentBundle\Plaid\Service\Processor;
use Erp\StripeBundle\Entity\PlaidBankAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PlaidBankAccountType extends AbstractType {

    /**
     * @var Item
     */
    private $itemPlaidService;

    /**
     * @var Processor
     */
    private $processorPlaidService;

    public function __construct(Item $itemPlaidService, Processor $processorPlaidService) {
        $this->itemPlaidService = $itemPlaidService;
        $this->processorPlaidService = $processorPlaidService;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('accountId', 'hidden', [
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Please press Verify button']),
                    ],
                ])
                ->add('publicToken', 'hidden', [
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Bank account not verified']),
                    ],
                ])
                ->add('submit', 'submit', [
                    'label' => 'Submit',
                    'attr' => [
                        'class' => 'btn-circle',
                    ]
                ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'postSubmit']);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(
                [
                    'data_class' => PlaidBankAccount::class,
                ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'erp_stripe_plaid_bank_account';
    }

    public function postSubmit(FormEvent $event) {
        $form = $event->getForm();
        /** @var PlaidBankAccount $plaidBankAccount */
        $plaidBankAccount = $event->getData();
        $publicToken = $plaidBankAccount->getPublicToken();
        $accountId = $plaidBankAccount->getAccountId();

        $response = $this->itemPlaidService->exchangePublicToken($publicToken);
        $result = json_decode($response['body'], true);

        if ($response['code'] < 200 || $response['code'] >= 300) {
            $form->addError(new FormError($result['display_message']));
            return;
        }

        $response = $this->processorPlaidService->createBankAccountToken($result['access_token'], $accountId);
        $result = json_decode($response['body'], true);

        if ($response['code'] < 200 || $response['code'] >= 300) {
            $form->addError(new FormError($result['display_message']));
            return;
        }

        $plaidBankAccount->setBankAccountToken($result['stripe_bank_account_token']);
    }

}
