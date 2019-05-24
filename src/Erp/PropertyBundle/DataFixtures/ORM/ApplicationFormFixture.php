<?php

namespace Erp\PropertyBundle\DataFixtures\ORM;

use Erp\PropertyBundle\Entity\ApplicationForm;
use Erp\PropertyBundle\Entity\ApplicationSection;
use Erp\PropertyBundle\Entity\ApplicationField;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ApplicationFormFixture extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $applicationForm = new ApplicationForm();
        $applicationForm->setCreatedDate();
        $applicationForm->setUpdatedDate();
        $applicationForm->setProperty(null);
        $applicationForm->setIsDefault(1);
        $applicationForm->setNoFee(1);
        $manager->persist($applicationForm);
        $manager->flush();

            $applicationSection = new ApplicationSection();
            $applicationSection->setApplicationForm($applicationForm);
            $applicationSection->setSort(1);
            $applicationSection->setName('Applicant Information');
            $applicationSection->setCreatedDate();
            $applicationSection->setUpdatedDate();
            $manager->persist($applicationSection);
            $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Name');
                $applicationField->setData(null);
                $applicationField->setSort(1);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Date of birth');
                $applicationField->setData(null);
                $applicationField->setSort(2);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('SSN');
                $applicationField->setData(null);
                $applicationField->setSort(3);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Phone');
                $applicationField->setData(null);
                $applicationField->setSort(4);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Current address');
                $applicationField->setData(null);
                $applicationField->setSort(5);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('City');
                $applicationField->setData(null);
                $applicationField->setSort(6);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('State');
                $applicationField->setData(null);
                $applicationField->setSort(7);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('ZIP Code');
                $applicationField->setData(null);
                $applicationField->setSort(8);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('radio');
                $applicationField->setName(null);
                $applicationField->setData('{"0":"Own","1":"Rent"}');
                $applicationField->setSort(9);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Monthly payment or rent');
                $applicationField->setData(null);
                $applicationField->setSort(10);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('How long?');
                $applicationField->setData(null);
                $applicationField->setSort(11);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Previous address');
                $applicationField->setData(null);
                $applicationField->setSort(12);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('City');
                $applicationField->setData(null);
                $applicationField->setSort(13);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('State');
                $applicationField->setData(null);
                $applicationField->setSort(14);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('ZIP Code');
                $applicationField->setData(null);
                $applicationField->setSort(15);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('radio');
                $applicationField->setName(null);
                $applicationField->setData('{"0":"Owned","1":"Rented"}');
                $applicationField->setSort(16);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Monthly payment or rent');
                $applicationField->setData(null);
                $applicationField->setSort(17);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('How long?');
                $applicationField->setData(null);
                $applicationField->setSort(18);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

            $applicationSection = new ApplicationSection();
            $applicationSection->setApplicationForm($applicationForm);
            $applicationSection->setSort(2);
            $applicationSection->setName('Employment Information');
            $applicationSection->setCreatedDate();
            $applicationSection->setUpdatedDate();
            $manager->persist($applicationSection);
            $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Current employer');
                $applicationField->setData(null);
                $applicationField->setSort(1);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Employer address');
                $applicationField->setData(null);
                $applicationField->setSort(2);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('How long?');
                $applicationField->setData(null);
                $applicationField->setSort(3);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Phone');
                $applicationField->setData(null);
                $applicationField->setSort(4);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('E-mail');
                $applicationField->setData(null);
                $applicationField->setSort(5);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Fax');
                $applicationField->setData(null);
                $applicationField->setSort(6);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('City');
                $applicationField->setData(null);
                $applicationField->setSort(7);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('State');
                $applicationField->setData(null);
                $applicationField->setSort(8);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('ZIP Code');
                $applicationField->setData(null);
                $applicationField->setSort(9);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Position');
                $applicationField->setData(null);
                $applicationField->setSort(10);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('radio');
                $applicationField->setName(null);
                $applicationField->setData('{"0":"Hourly","1":"Salary"}');
                $applicationField->setSort(11);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Annual income');
                $applicationField->setData(null);
                $applicationField->setSort(12);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

            $applicationSection = new ApplicationSection();
            $applicationSection->setApplicationForm($applicationForm);
            $applicationSection->setSort(3);
            $applicationSection->setName('Emergency Contact');
            $applicationSection->setCreatedDate();
            $applicationSection->setUpdatedDate();
            $manager->persist($applicationSection);
            $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Name of a person not residing with you');
                $applicationField->setData(null);
                $applicationField->setSort(1);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Address');
                $applicationField->setData(null);
                $applicationField->setSort(2);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('City');
                $applicationField->setData(null);
                $applicationField->setSort(3);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('State');
                $applicationField->setData(null);
                $applicationField->setSort(4);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('ZIP Code');
                $applicationField->setData(null);
                $applicationField->setSort(5);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Phone');
                $applicationField->setData(null);
                $applicationField->setSort(6);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Relationship');
                $applicationField->setData(null);
                $applicationField->setSort(7);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();


            $applicationSection = new ApplicationSection();
            $applicationSection->setApplicationForm($applicationForm);
            $applicationSection->setSort(4);
            $applicationSection->setName('Co-applicant Information, if Married');
            $applicationSection->setCreatedDate();
            $applicationSection->setUpdatedDate();
            $manager->persist($applicationSection);
            $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Name');
                $applicationField->setData(null);
                $applicationField->setSort(1);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Date of birth');
                $applicationField->setData(null);
                $applicationField->setSort(2);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('SSN');
                $applicationField->setData(null);
                $applicationField->setSort(3);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Phone');
                $applicationField->setData(null);
                $applicationField->setSort(4);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Current address');
                $applicationField->setData(null);
                $applicationField->setSort(5);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('City');
                $applicationField->setData(null);
                $applicationField->setSort(6);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('State');
                $applicationField->setData(null);
                $applicationField->setSort(7);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('ZIP Code');
                $applicationField->setData(null);
                $applicationField->setSort(8);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('radio');
                $applicationField->setName(null);
                $applicationField->setData('{"0":"Own","1":"Rent"}');
                $applicationField->setSort(9);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Monthly payment or rent');
                $applicationField->setData(null);
                $applicationField->setSort(10);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('How long?');
                $applicationField->setData(null);
                $applicationField->setSort(11);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Previous address');
                $applicationField->setData(null);
                $applicationField->setSort(12);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('City');
                $applicationField->setData(null);
                $applicationField->setSort(13);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('State');
                $applicationField->setData(null);
                $applicationField->setSort(14);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('ZIP Code');
                $applicationField->setData(null);
                $applicationField->setSort(15);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('radio');
                $applicationField->setName(null);
                $applicationField->setData('{"0":"Owned","1":"Rented"}');
                $applicationField->setSort(16);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Monthly payment or rent');
                $applicationField->setData(null);
                $applicationField->setSort(17);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('How long?');
                $applicationField->setData(null);
                $applicationField->setSort(18);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

            $applicationSection = new ApplicationSection();
            $applicationSection->setApplicationForm($applicationForm);
            $applicationSection->setSort(5);
            $applicationSection->setName('Co-applicant Employment Information');
            $applicationSection->setCreatedDate();
            $applicationSection->setUpdatedDate();
            $manager->persist($applicationSection);
            $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Current employer');
                $applicationField->setData(null);
                $applicationField->setSort(1);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Employer address');
                $applicationField->setData(null);
                $applicationField->setSort(2);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('How long?');
                $applicationField->setData(null);
                $applicationField->setSort(3);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Phone');
                $applicationField->setData(null);
                $applicationField->setSort(4);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('E-mail');
                $applicationField->setData(null);
                $applicationField->setSort(5);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Fax');
                $applicationField->setData(null);
                $applicationField->setSort(6);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('City');
                $applicationField->setData(null);
                $applicationField->setSort(7);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('State');
                $applicationField->setData(null);
                $applicationField->setSort(8);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('ZIP Code');
                $applicationField->setData(null);
                $applicationField->setSort(9);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Position');
                $applicationField->setData(null);
                $applicationField->setSort(10);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('radio');
                $applicationField->setName(null);
                $applicationField->setData('{"0":"Hourly","1":"Salary"}');
                $applicationField->setSort(11);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Annual income');
                $applicationField->setData(null);
                $applicationField->setSort(12);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

            $applicationSection = new ApplicationSection();
            $applicationSection->setApplicationForm($applicationForm);
            $applicationSection->setSort(6);
            $applicationSection->setName('References');
            $applicationSection->setCreatedDate();
            $applicationSection->setUpdatedDate();
            $manager->persist($applicationSection);
            $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Name');
                $applicationField->setData(null);
                $applicationField->setSort(1);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Address');
                $applicationField->setData(null);
                $applicationField->setSort(1);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();

                $applicationField = new ApplicationField();
                $applicationField->setApplicationSection($applicationSection);
                $applicationField->setType('text');
                $applicationField->setName('Phone');
                $applicationField->setData(null);
                $applicationField->setSort(1);
                $applicationField->setUpdatedDate();
                $applicationField->setCreatedDate();
                $manager->persist($applicationField);
                $manager->flush();
    }
}