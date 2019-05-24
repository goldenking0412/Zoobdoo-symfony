<?php

namespace Erp\NotificationBundle\Form\Type;

use Erp\NotificationBundle\Entity\UserNotification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Erp\NotificationBundle\Entity\Template;
use Doctrine\ORM\EntityRepository;

class UserNotificationType extends AbstractType {

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $templates = $options['templates'] ? $options['templates'] : [];
        $templatesSms = $options['templatesSms'] ? $options['templatesSms'] : [];
        $builder
                ->add('template', EntityType::class, [
                    'required' => true,
                    'class' => Template::class,
                    'property' => 'title',
                    'choices' => $templates
                ])
                ->add('templateSms', EntityType::class, [
                    'required' => false,
                    'class' => Template::class,
                    'property' => 'title',
                    'choices' => $templatesSms,
                    'empty_value' => 'Select Template...'
                ])
                ->add('notifications', CollectionType::class, [
                    'label' => false,
                    'entry_type' => NotificationType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ])
                ->add('alerts', CollectionType::class, [
                    'label' => false,
                    'entry_type' => AlertType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ])
                ->add('sendNotificationAutomatically', CheckboxType::class, [
                    'required' => false,
                    'label' => 'Automatically send Notification on Rent Due Date?',
                ])
                ->add('sendSms', CheckboxType::class, [
                    'required' => false,
                    'label' => 'Sent Text Alerts too?',
                ])
//                ->add('sendAlertAutomatically', CheckboxType::class, [
//                    'required' => false,
//                    'label' => 'Automatically send Alert on Rent Due Date?',
//                ])
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
            'data_class' => UserNotification::class,
            'csrf_protection' => false,
            'templates' => [],
            'templatesSms' => [],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'erp_notification_user_notification';
    }

}
