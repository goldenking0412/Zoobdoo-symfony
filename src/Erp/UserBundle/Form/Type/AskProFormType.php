<?php
namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class AskProFormType extends AbstractType
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
        $this->validationGroup = 'ProRequestCreate';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this->addSubject()
            ->addText()
            ->addIsReveral();
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'        => 'Erp\UserBundle\Entity\ProRequest',
                'validation_groups' => [$this->validationGroup]
            ]
        );
    }

    public function getName()
    {
        return 'erp_users_ask_pro_form';
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
                'label'    => ' ',
                'attr'     => [
                    'class' => 'form-control subject',
                    'placeholder' => 'Subject',
                    'maxlength' => 255,
                ],
                'required' => true
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
            'message',
            'textarea',
            [
                'label'    => ' ',
                'attr'     => [
                    'class' => 'full-width form-control',
                    'placeholder' => 'Message',
                    'rows' => 5,
                    'maxlength' => 1000,
                ],
                'required' => true
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addIsReveral()
    {
        $this->formBuilder->add(
            'isRefferal',
            'checkbox',
            [
                'required' => false,
                'label'    => ' I have a property that I would like to buy/sell',
            ]
        );

        return $this;
    }
}
