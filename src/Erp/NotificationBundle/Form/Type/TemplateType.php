<?php

namespace Erp\NotificationBundle\Form\Type;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\NotificationBundle\Entity\Template;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class TemplateType extends AbstractType {

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('type', TextType::class, [
                    'label' => 'Type',
                    'constraints' => [
                        new Assert\NotBlank(),
                    ]
                ])
                ->add('title', TextType::class, [
                    'label' => 'Title',
                    'constraints' => [
                        new Assert\NotBlank(),
                    ]
                ])
                ->add('isSms', HiddenType::class)
                ->add('description', CkeditorType::class, [
                    'label' => 'Description',
                    'constraints' => [
                        new Assert\NotBlank(),
                    ]
                ])
        ;


        if ($options['is_sms'] == 1) {
            $builder
                    ->add('descriptionSms', TextType::class, array('label' => 'Text for Sms', 'constraints' => [ new Assert\NotBlank(),])
            );
        }
        
        $builder
                ->add('submit', SubmitType::class, [
                    'label' => 'Save',
                ])
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Template::class,
            'is_sms' => false
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'erp_notification_template';
    }

}
