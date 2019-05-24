<?php

namespace Erp\NotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Erp\NotificationBundle\Entity\Notification;

class NotificationType extends AbstractType {

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('daysBefore', TextType::class, [
                    'required' => false,
                    'label' => 'Days Before Rent Due Date',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label_attr' => [
                        'class' => 'control-label'
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Type('numeric'),
                        new Assert\GreaterThanOrEqual(['value' => 1]),
                    ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Notification::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'erp_notification_notification';
    }

}
