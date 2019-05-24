<?php

namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DashboardServiceRequestFormType extends AbstractType {

    /**
     * @var string
     */
    protected $validationGroup = '';

    /**
     * @var string
     */
    protected $translationDomain = 'FOSUserBundle';

    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;
    
    protected $types;

    /**
     * Construct method
     */
    public function __construct($types = []) {
        $this->types = $types;
        $this->validationGroup = 'ServiceRequest';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $options['showSendSms'] = isset($options['showSendSms']) ? $options['showSendSms'] : false;
        $this->formBuilder = $builder;
        $this
                ->addDate()
                ->addSubject()
                ->addType()
                ->addText()
        ;
        if ($options['showSendSms']) {
            $this->addSendSms();
        }
        $this->formBuilder->add('attachments', 'file', array(
            'multiple' => true,
            'required' => false,
        ));
        /* $this->formBuilder->add('documents', 'collection',
          [
          'type' => new DocumentType(['inputClass' => 'form-control upload-input']),
          'required' => false,
          'allow_add'    => true,
          'allow_delete'  => true,
          'delete_empty'  => true,
          'by_reference' => false,
          'attr' => [
          'nested_form' => true,
          'nested_form_min' => 0,
          ],
          ]
          ); */

        $this->formBuilder->add('save', 'submit', ['label' => 'Submit', 'attr' => ['class' => 'btn-circle']]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                [
                    'data_class' => 'Erp\UserBundle\Entity\ServiceRequest',
                    'validation_groups' => [$this->validationGroup],
                    'showSendSms' => false
                ]
        );
    }

    /**
     * @return string
     */
    public function getName() {
        return 'erp_users_form_service_request';
    }

    /**
     * Return list service request types
     *
     * @return array
     */
    protected function getTypes() {
        return $this->types;
    }

    /**
     * @return $this
     */
    protected function addType() {
        $this->formBuilder->add('typeId', 'choice', [
            'label' => ' ',
            'attr' => [
                'class' => 'select-control',
            ],
            'choices' => $this->getTypes(),
            'required' => true
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function addDate() {
        $this->formBuilder->add('date', 'text', [
            'label' => ' ',
            'attr' => ['class' => 'date', 'placeholder' => 'Date'],
            'required' => true,
            'constraints' => [
                new Regex([
                    'pattern' => '/^[\d]{2}\/[\d]{2}\/[\d]{4}$/',
                    'message' => 'Date isn\'t valid',
                    'groups' => ['ServiceRequest'],
                        ]),
            ]
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function addText() {
        $this->formBuilder->add('text', 'textarea', [
            'label' => ' ',
            'attr' => [
                'class' => 'full-width',
                'placeholder' => 'Message',
                'maxlength' => 1000,
            ],
            'required' => true,
        ]);

        return $this;
    }

    protected function addSendSms() {
        $this->formBuilder->add('sendSms', 'checkbox', [
            'label' => 'Send Message Via Text?',
            'attr' => ['class' => ''],
            'label_attr' => ['class' => 'control-label'],
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function addSubject() {
        $this->formBuilder->add('subject', 'text', [
            'label' => ' ',
            'attr' => ['class' => '', 'placeholder' => 'Subject', 'maxlength' => 255],
            'required' => false,
        ]);

        return $this;
    }

}
