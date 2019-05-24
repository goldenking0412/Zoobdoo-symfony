<?php

namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\PropertyBundle\Entity\ApplicationForm;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ApplicationFeeType extends AbstractType {

    const NAME = 'erp_property_application_fee';

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('noFee', 'checkbox', [
                    'label' => 'No fee',
                    'required' => false
                ])
                ->add('fee', 'text', [
                    'label' => 'Fee',
                    'required' => false,
                    'constraints' => [new Assert\Callback([$this, 'validateFee'])]
                ])
                ->add('submit', 'submit', [
                    'label' => 'Submit'
                ])
        ;
    }


    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => ApplicationForm::class
        ]);
    }

    public function validateFee($value, ExecutionContextInterface $context) {
        $form = $context->getRoot();
        /** @var ApplicationForm $data */
        $data = $form->getData();
        if (!$data->isNoFee() && ($value <= 0 || $value == '')) {
            $context
                    ->buildViolation('Fee must be turned off or greater than 0.')
                    ->addViolation();
        }
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return self::NAME;
    }

}
