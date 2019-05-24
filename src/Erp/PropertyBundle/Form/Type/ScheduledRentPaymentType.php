<?php

namespace Erp\PropertyBundle\Form\Type;

use Erp\PropertyBundle\Entity\ScheduledRentPayment;
use Erp\PropertyBundle\Entity\PropertySecurityDeposit;
use Erp\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Erp\CoreBundle\Formatter\MoneyFormatter;

class ScheduledRentPaymentType extends AbstractType {

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var MoneyFormatter
     */
    private $formatter;

    public function __construct(TokenStorageInterface $tokenStorage, MoneyFormatter $formatter) {
        $this->tokenStorage = $tokenStorage;
        $this->formatter = $formatter;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user->hasRole(User::ROLE_TENANT)) {
            return new \RuntimeException('%s can use only %s', self::class, User::ROLE_TENANT);
        }

        $choices = $this->getCategoryChoices($user);

        $builder
                ->add('amount', 'number', [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control subject',
                        'placeholder' => 'Amount $',
                    ],
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Amount should not be empty']),
                        new Assert\Length(
                                [
                            'min' => 1,
                            'max' => 4,
                            'minMessage' => 'Enter the amount in the range from $1 to $9999',
                            'maxMessage' => 'Enter the amount in the range from $1 to $9999',
                                ]),
                        new Assert\Range(
                                [
                            'min' => 0.01,
                            'max' => 1000000,
                            'minMessage' => 'Amount should have minimum 0.01$ and maximum $1,000,000',
                            'maxMessage' => 'Amount should have minimum 0.01$ and maximum $1,000,000',
                                ])
                    ],
                ])
                ->add('startPaymentAt', 'date', [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control subject date',
                        'placeholder' => 'Date',
                    ],
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Payment Date should not be empty']),
                        new Assert\Date(['message' => 'Is not a valid date format']),
                        new Assert\GreaterThanOrEqual(['value' => 'today', 'message' => 'Please select today\'s or future date.']),
                    ],
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ])
                ->add('category', 'choice', [
                    'label' => 'Category',
                    'choices' => $choices,
                ])
                ->add('type', 'checkbox', [
                    'required' => false,
                    'label' => 'Make this a recurring payment',
                    'label_attr' => ['class' => 'control-label'],
                ])
                ->add('submit', 'submit', [
                    'label' => 'Make Payment',
                    'attr' => ['class' => 'btn-circle'],
                ])
        ;


        $property = $user->getTenantProperty();
        if ($property) {
            $propertySettings = $property->getSettings();
            if ($propertySettings && $propertySettings->isAllowAutoDraft()) {
                $builder->add('agreeAutoWithdrawal', 'checkbox', [
                    'required' => false,
                    'label' => 'Allow auto withdrawal',
                    'label_attr' => ['class' => 'control-label'],
                ]);
            }
        }

        $builder->get('type')
                ->addViewTransformer(new CallbackTransformer(
                        function ($type) {
                    return $type === ScheduledRentPayment::TYPE_RECURRING;
                }, function ($type) {
                    return $type ? ScheduledRentPayment::TYPE_RECURRING : ScheduledRentPayment::TYPE_SINGLE;
                }
                        ), true);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => ScheduledRentPayment::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'erp_property_scheduled_rent_payment';
    }

    /**
     * 
     */
    private function getCategoryChoices(User $user) {
        $choices = [];
        if ($user->isDebtor()) {
            $amount = $this->formatter->format($user->getTotalOwedAmount());
            return [ScheduledRentPayment::CATEGORY_LATE_RENT_PAYMENT => sprintf('Late Rent Payment (%s)', $amount)];
        }

        $property = $user->getTenantProperty();
        $alreadyPaidDeposit = $property->getSecurityDeposit() && ($property->getSecurityDeposit()->getStatus() != PropertySecurityDeposit::STATUS_DEPOSIT_UNPAID && $property->getSecurityDeposit()->getStatus() != false);

        if (!$property) {
            return new \RuntimeException('Tenant must have property.');
        }

        $propertyDeposit = $property->getSecurityDeposit();

        if ($propertyDeposit && $propertyDeposit->getYesWantSecurityDeposit() == 1 && !$alreadyPaidDeposit) {
            $now = new \DateTime();
            $currentDayOfMonth = $now->format('j');
            $amount = $propertyDeposit->getAmount();
            $amount = $this->formatter->format($amount);

            $choices[ScheduledRentPayment::CATEGORY_DEPOSIT_PAYMENT] = sprintf('Deposit Payment (%s)', $amount);
        }

        $propertySettings = $property->getSettings();
        if ($propertySettings) {
            $dayUntilDue = $propertySettings->getDayUntilDue();

            $now = new \DateTime();
            $currentDayOfMonth = $now->format('j');

            $amount = 0;
            if ($currentDayOfMonth > $dayUntilDue) {
                $amount = $propertySettings->getPaymentAmount();
            }

            $amount = $this->formatter->format($amount);

            $choices[ScheduledRentPayment::CATEGORY_RENT_PAYMENT] = sprintf('Rent Payment (%s)', $amount);
            return $choices;
        }
    }
}