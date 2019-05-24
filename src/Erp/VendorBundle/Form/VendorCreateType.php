<?php

namespace Erp\VendorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class VendorCreateType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder
                ->add('email', 'email', ['label' => 'Email', 'required' => true, 'attr' => ['class' => 'form-control'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Email cannot be empty.',
                            'groups' => ['LandlordDetails']
                                ]
                        ),
                        new Email(
                                [
                            'message' => 'Lanlord must have valid email.',
                            'groups' => ['LandlordDetails']
                                ]
                        )
                    ]
                ])
                ->add('name', 'text', ['label' => 'Name', 'required' => true, 'attr' => ['class' => 'form-control', 'pattern' => '.{2,255}'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Please enter Name',
                            'groups' => ['LandlordDetails']
                                ]
                        ),
                        new Length(
                                [
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => 'Name cannot be less than 2 chars',
                            'maxMessage' => 'Name cannot be longer than 255 chars',
                            'groups' => ['LandlordDetails']
                                ]
                        )
                    ]
                ])
                ->add('submit', 'submit', [
                    'label' => 'Create',
                    'attr' => [
                        'class' => 'blue'
                    ]
                ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Erp\VendorBundle\Entity\VendorCreate'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'erp_vendorbundle_vendorcreate';
    }

}
