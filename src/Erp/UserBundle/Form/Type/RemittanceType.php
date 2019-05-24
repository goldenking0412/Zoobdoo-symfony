<?php

namespace Erp\UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Erp\UserBundle\Entity\User;
use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\Remittance;
use Erp\CoreBundle\Form\DocumentType;

class RemittanceType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $remittance = $options['data'];
        $currency = ($remittance->getCurrency()) ? $remittance->getCurrency() : 'USD';
        $manager = $remittance->getManager();

        $builder
                ->add('amount', MoneyType::class, array(
                    'label' => 'Amount of remittance',
                    'currency' => $currency
                ))
                ->add('type', ChoiceType::class, array(
                    'label' => 'Type of remittance',
                    'choices' => Remittance::getTypeOptions(),
                    'mapped' => false
                ))
                ->add('comment', TextareaType::class, array(
                    'label' => 'You can add a comment',
                    'required' => false,
                    'attr' => array('rows' => '8')
                ))
                ->add('toUser', EntityType::class, array(
                    'class' => User::class,
                    'label' => 'Charge this remittance to the following tenant or landlord',
                    'query_builder' => function (EntityRepository $er) use($manager) {
                        $qb = $er->createQueryBuilder('u');

                        return $qb
                                ->where($qb->expr()->orX(
                                                $qb->expr()->in('u', ':landlords'),
                                                $qb->expr()->in('u', ':tenants')
                                        )
                                )
                                ->andWhere($qb->expr()->eq('u.isTermOfUse', true))
                                ->andWhere($qb->expr()->eq('u.status', ':active'))
                                ->andWhere($qb->expr()->neq('u.locked', true))
                                ->andWhere($qb->expr()->neq('u.expired', true))
                                ->setParameter('landlords', $manager->getLandlords())
                                ->setParameter('tenants', $manager->getTenants())
                                ->setParameter('active', User::STATUS_ACTIVE)
                        ;
                    }
                ))
                ->add('document', DocumentType::class, array(
                    'label' => 'Upload Your File (PDF, JPG, PNG, GIF, TIF, DOC, DOCX)'
                ))
                ->add('property', EntityType::class, array(
                    'class' => Property::class,
                    'label' => 'Property involved by remittance',
                    'query_builder' => function (EntityRepository $er) use ($manager) {
                        $qb = $er->createQueryBuilder('p');

                        return $qb
                                ->join(User::class, 'u', Expr\Join::WITH, $qb->expr()->eq('p.user', 'u'))
                                ->where($qb->expr()->eq('u', ':user'))
                                ->andWhere($qb->expr()->neq('p.status', ':statusDeleted'))
                                ->setParameter('user', $manager)
                                ->setParameter('statusDeleted', Property::STATUS_DELETED)
                                ->orderBy('p.name')
                        ;
                    }
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Erp\UserBundle\Entity\Remittance',
            'validation_groups' => array('Remittances'),
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'erp_userbundle_remittance';
    }

}
