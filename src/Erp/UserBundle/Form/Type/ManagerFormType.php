<?php

namespace Erp\UserBundle\Form\Type;

use Erp\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ManagerFormType extends AbstractType {

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('firstName', 'text', ['label' => 'First name', 'required' => true, 'attr' => ['class' => 'form-control', 'pattern' => '.{2,255}'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Please enter manager First name',
                            'groups' => ['ManagerDetails']
                                ]
                        ),
                        new Length(
                                [
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => 'First name cannot be less than 2 chars',
                            'maxMessage' => 'First name cannot be longer than 255 chars',
                            'groups' => ['ManagerDetails']
                                ]
                        )
                    ]
                ])
                ->add('lastName', 'text', ['label' => 'Last name', 'required' => true, 'attr' => ['class' => 'form-control', 'pattern' => '.{2,255}'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Please enter manager Last name',
                            'groups' => ['ManagerDetails']
                                ]
                        ),
                        new Length(
                                [
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => 'Last name cannot be less than 2 chars',
                            'maxMessage' => 'Last name cannot be longer than 255 chars',
                            'groups' => ['ManagerDetails']
                                ]
                        )
                    ]
                ])
                ->add('phone', 'text', ['required' => true, 'attr' => ['class' => 'form-control']])
                ->add('email', 'email', ['required' => true, 'attr' => ['class' => 'form-control'],
                    'constraints' => [
                        new NotBlank(
                                [
                            'message' => 'Email cannot be empty.',
                            'groups' => ['ManagerDetails']
                                ]
                        ),
                        new Email(
                                [
                            'message' => 'Manager must have valid email.',
                            'groups' => ['ManagerDetails']
                                ]
                        )
                    ]
                ])
                ->add('addressOne', 'text', ['label' => 'Address', 'required' => false, 'attr' => ['class' => 'form-control']])
        // ->add('submit', 'submit', ['label' => 'Save', 'attr' => ['class' => 'btn edit-btn btn-space']])
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['ManagerDetails']
        ]);
    }

    public function getName() {
        return 'erp_user_managers_create';
    }

}
