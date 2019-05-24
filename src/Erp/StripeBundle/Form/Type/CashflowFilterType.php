<?php

namespace Erp\StripeBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\StripeBundle\Entity\Transaction;

class CashflowFilterType extends AbstractFilterType
{
    const DATE_FORMAT = 'Y-m';

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'dateFrom',
                'date',
                [
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM',
                ]
            )
            ->add(
                'dateTo',
                'date',
                [
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM',
                ]
            )
            ->add(
                'type',
                'choice',
                [
                    'choices' => [
                        Transaction::CASH_IN => Transaction::CASH_IN,
                        Transaction::CASH_OUT => Transaction::CASH_OUT,
                    ],
                ]
            );

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'preSubmit']);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (isset($data['dateFrom']) && !isset($data['dateTo'])) {
            $dateTo = \DateTime::createFromFormat(self::DATE_FORMAT, $data['dateFrom'])->modify('first day of this month +1 month')->setTime(0, 0, 0);
            $data['dateTo'] = $dateTo->format(self::DATE_FORMAT);
            $event->setData($data);
        }
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET',
        ]);
    }
}
