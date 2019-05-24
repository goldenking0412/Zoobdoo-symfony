<?php

namespace Erp\PropertyBundle\Validator\Constraints;

use Erp\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;

class ScheduledRentPaymentClassValidator extends ConstraintValidator {

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @inheritdoc
     */
    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof ScheduledRentPaymentClass) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Length');
        }

        if (null === $value) {
            return;
        }

        /** @var User $user */
        if (!$user = $this->tokenStorage->getToken()->getUser()) {
            throw new \RuntimeException('User must be specified.');
        }

        if (!$user->hasRole(User::ROLE_TENANT)) {
            throw new \RuntimeException('Only tenant can pay for rent.');
        }

        if (!$property = $user->getTenantProperty()) {
            throw new \RuntimeException('User must have the property.');
        }

        if (!$propertySettings = $property->getSettings()) {
            throw new \RuntimeException('Property must have the payment settings.');
        }

        $userDayDue = $value->getStartPaymentAt()->format('j');
        $dayUntilDue = $propertySettings->getDayUntilDue();

//        if ($dayUntilDue != $userDayDue) {
//            if ($this->context instanceof ExecutionContextInterface) {
//                $this->context->buildViolation($constraint->dayUntilDueMessage)
//                    ->setParameter('{{ value }}', $userDayDue)
//                    ->setInvalidValue($userDayDue)
//                    ->addViolation();
//            } else {
//                $this->buildViolation($constraint->dayUntilDueMessage)
//                    ->setParameter('{{ value }}', $userDayDue)
//                    ->setInvalidValue($userDayDue)
//                    ->addViolation();
//            }
//        }
        if ($value->getCategory() == 'deposit_payment') {
            $propertyDeposit = $property->getSecurityDeposit();
            if ($propertyDeposit->getYesWantSecurityDeposit() != 1 ) {
                if ($this->context instanceof ExecutionContextInterface) {
                    $this->context->buildViolation($constraint->depositWantSecurityMessage)
                        ->addViolation();
                } else {
                    $this->buildViolation($constraint->depositWantSecurityMessage)
                        ->addViolation();
                }
            }      
            if ($value->getAmount() != $propertyDeposit->getAmount()) {
                if ($this->context instanceof ExecutionContextInterface) {
                    $this->context->buildViolation($constraint->depositAmountMessage)
                        ->setParameter('{{ value }}', $value->getAmount())
                        ->setInvalidValue($value->getAmount())
                        ->addViolation();
                } else {
                    $this->buildViolation($constraint->depositAmountMessage)
                        ->setParameter('{{ value }}', $value->getAmount())
                        ->setInvalidValue($value->getAmount())
                        ->addViolation();
                }
            }
      
        } else {
            if ($value->getAmount() != $propertySettings->getPaymentAmount()) {
                if ($this->context instanceof ExecutionContextInterface) {
                    $this->context->buildViolation($constraint->paymentAmountMessage)
                        ->setParameter('{{ value }}', $value->getAmount())
                        ->setInvalidValue($value->getAmount())
                        ->addViolation();
                } else {
                    $this->buildViolation($constraint->paymentAmountMessage)
                        ->setParameter('{{ value }}', $value->getAmount())
                        ->setInvalidValue($value->getAmount())
                        ->addViolation();
                }
            }

            if ($value->isRecurring()) {
                if (!$propertySettings->getAllowAutoDraft()) {
                    if ($this->context instanceof ExecutionContextInterface) {
                        $this->context->buildViolation($constraint->allowAutoDraftMessage)
                            ->addViolation();
                    } else {
                        $this->buildViolation($constraint->allowAutoDraftMessage)
                            ->addViolation();
                    }
                }
            }

            $rentPaymentBalance = $user->getRentPaymentBalance();
            if ($rentPaymentBalance && $rentPaymentBalance->getBalance() < 0) {
                if ($value->getCategory() == ScheduledRentPayment::CATEGORY_RENT_PAYMENT && !$user->isAllowRentPayment()) {
                    if ($this->context instanceof ExecutionContextInterface) {
                        $this->context->buildViolation($constraint->allowRentPaymentMessage)
                            ->addViolation();
                    } else {
                        $this->buildViolation($constraint->allowRentPaymentMessage)
                            ->addViolation();
                    }
                }
            }  
        }
    }

}
