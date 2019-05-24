<?php
namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class ForumCommentFormType extends AbstractType
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
        $this->validationGroup = 'CreatedForumComment';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this->addText();

        $this->formBuilder->add(
            'submit',
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
                'data_class' => 'Erp\UserBundle\Entity\ForumComment',
                'validation_groups' => [$this->validationGroup]
            ]
        );
    }

    public function getName()
    {
        return 'erp_user_form_forum_comment';
    }

    /**
     * @return $this
     */
    protected function addText()
    {
        $this->formBuilder->add(
            'text',
            'text',
            [
                'label' => false,
                'attr' => [
                    'class' => 'form-control field-text',
                    'placeholder' => 'Leave a Message',
                    'maxlength' => 1000,
                ],
                'required' => true,
            ]
        );

        return $this;
    }
}
