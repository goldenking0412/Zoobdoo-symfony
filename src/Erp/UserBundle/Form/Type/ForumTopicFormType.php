<?php
namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class ForumTopicFormType extends AbstractType
{
    /**
     * @var string
     */
    protected $validationGroup = '';


    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * Construct method
     */
    public function __construct()
    {
        $this->validationGroup = 'CreatedForumTopic';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this
            ->addName()
            ->addText()
        ;

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
                'data_class'        => 'Erp\UserBundle\Entity\ForumTopic',
                'validation_groups' => [$this->validationGroup]
            ]
        );
    }

    public function getName()
    {
        return 'erp_user_form_forum_topic';
    }

    /**
     * @return $this
     */
    protected function addName()
    {
        $this->formBuilder->add(
            'name',
            'text',
            [
                'label'              => ' ',
                'attr'               => ['class' => 'form-control', 'placeholder' => 'Title'],
                'required'           => true,
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
                'attr'               => ['class' => 'form-control', 'placeholder' => 'Text' ],
                'required'           => true,
            ]
        );

        return $this;
    }
}
