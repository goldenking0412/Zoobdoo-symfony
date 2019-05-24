<?php

namespace Erp\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactPageRequestFormType extends AbstractType {

    /**
     * Construct method
     */
    public function __construct() {
        $this->validationGroup = 'ContactPageRequest';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', TextType::class, array(
            'attr' => ['class' => 'form-control'],
            'label' => 'First Name',
            'required' => true
        ));

        $builder->add('surname', EmailType::class, array(
            'attr' => ['class' => 'form-control'],
            'label' => 'Last Name',
            'required' => true
        ));

        $builder->add('email', EmailType::class, array(
            'attr' => ['class' => 'form-control'],
            'required' => true
        ));

        $builder->add('phone', TextType::class, array(
            'attr' => ['class' => 'form-control'],
            'required' => false
        ));

        $builder->add('subject', TextType::class, array(
            'attr' => ['class' => 'form-control'],
            'required' => true
        ));

        $builder->add('message', TextareaType::class, array(
            'attr' => ['class' => 'form-control'],
            'required' => true
        ));

        $builder->add(
                'submit', SubmitType::class, ['label' => 'Send', 'attr' => ['class' => 'btn btn-default red']]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                [
                    'data_class' => 'Erp\SiteBundle\Entity\ContactPageRequest',
                    'validation_groups' => [$this->validationGroup]
                ]
        );
    }

    /**
     * @return string
     */
    public function getName() {
        return 'erp_sitebundle_contactpagerequest';
    }

}
