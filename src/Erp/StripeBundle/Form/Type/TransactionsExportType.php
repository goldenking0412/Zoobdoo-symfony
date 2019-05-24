<?php

namespace Erp\StripeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\StripeBundle\Entity\TransactionsExport;

class TransactionsExportType extends AbstractType
{
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
                    'label' => 'Start Date:',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => ['class' => 'form-control col-xs-4 date'],
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'required' => false,
                ]
            )
            ->add(
                'dateTo',
                'date',
                [
                    'label' => 'End Date:',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => ['class' => 'form-control col-xs-4 date'],
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'required' => false,
                ]
            )
            ->add(
                'pdf_submit',
                'submit',
                [
                    'label' => 'pdf',
                    'attr' => [
                        'class' => 'btn edit-btn'
                    ]
                ]
            )
            ->add(
                'csv_submit',
                'submit',
                [
                    'label' => 'csv',
                    'attr' => [
                        'class' => 'btn edit-btn'
                    ]
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TransactionsExport::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_stripe_transactions_export';
    }
}