<?php
namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class MessagesFormType extends AbstractType
{
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

    /**
     * Construct method
     */
    public function __construct()
    {
        $this->validationGroup = 'Messages';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['showSendSms'] = isset($options['showSendSms']) ? $options['showSendSms'] : false;
        $this->formBuilder = $builder;
        $this
            ->addSubject()
            ->addText()
        ;
        if ($options['showSendSms']) {
            $this->addSendSms();
        }

        $this->formBuilder->add(
            'save',
            'submit',
            ['label' => 'Submit', 'attr' => ['class' => 'btn-circle']]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'        => 'Erp\UserBundle\Entity\Message',
                'validation_groups' => [$this->validationGroup],
                'showSendSms' => false,
            ]
        );
    }

    public function getName()
    {
        return 'erp_users_form_messages';
    }

    /**
     * @return $this
     */
    protected function addSubject()
    {
        $this->formBuilder->add(
            'subject',
            'text',
            [
                'label'              => ' ',
                'attr'               => ['class' => 'form-control', 'placeholder' => 'Subject', 'maxlength' => 255],
                'required'           => false,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addText()
    {
        $this->formBuilder->add(
            'text',
            'textarea',
            [
                'label'              => ' ',
                'attr'               => ['class' => 'form-control', 'placeholder' => 'Message', 'maxlength' => 255],
                'required'           => true,
            ]
        );

        return $this;
    }
    protected function addSendSms()
    {
        $this->formBuilder->add(
            'sendSms',
            'checkbox',
            [
                'label'              => 'Send Message Via Text?',
                'attr'               => ['class' => ''],
                'label_attr'         => ['class' => 'control-label'],
                'required'           => false,
            ]
        );

        return $this;
    }
}
