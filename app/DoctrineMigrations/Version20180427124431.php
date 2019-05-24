<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180427124431 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE static_pages SET content=\'<h3>What is Zoobdoo?</h3>

<p>Our service allows property&nbsp;managers to&nbsp;build their business by keeping things simple and affordable. Now property managers have real options.&nbsp;manage your rents, get background and&nbsp;<a href="https://zoobdoo.com/online-tenant-screening">credit checks for property managers</a>, create rental contracts along with receiving online digital signatures,&nbsp;<a href="https://zoobdoo.com/pay-rent-online">accept rent payments online</a>, send rental applications and collect fees, and even post&nbsp;<a href="https://zoobdoo.com/post-apartment-for-rent">apartments for rent</a>&nbsp;from the Zoobdoo website onto other public sites.&nbsp;</p>

<h3>&nbsp;</h3>

<h3>How do the transactions occur?</h3>

<p>The tenant submits payment through Zoobdoo tenant portal, payment processes through the Automatic Clearing House (ACH) or Credit Processor (CC), and payment is deposited directly in property managers account.&nbsp;</p>

<p>&nbsp;</p>

<h3>Price: How much does it cost?</h3>

<ul><li>Property Managers pay nothing!&nbsp;No setup or monthly fees! No contracts or obligations!&nbsp;</li>
        <li>Tenant pays&nbsp;a small $3.00 flat fee per ACH transaction.</li>
        <li>Tenant pays&nbsp;a 2.5% per Credit Card transaction. (Accept Visa, Mastercard and Discover)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
        <li>Application fee per applicant submission,&nbsp;$1.00.&nbsp;(Perspective Tenant pays during application process)</li>
        <li>Credit, background, and eviction check,&nbsp;$15.</li>
        <li>Posting properties online, FREE.</li>
</ul><p>&nbsp;</p>

<h3>Do I have to sign a contract?</h3>

<p>No, there is no long or short term contract or obligation. You can cancel at anytime.</p>

<p>&nbsp;</p>

<h3>Are there any hidden fees or obligations?</h3>

<p>There are no hidden fees.&nbsp;No contracts or obligations.</p>

<p>&nbsp;</p>

<h3>How do I start accepting tenant payments directly to my account?&nbsp;</h3>

<p>For your security we require the following documents be uploaded using the SendSafely secured and encrypted link.</p>

<ul><li><em><strong>A copy of a voided business bank account check for account to be used.</strong></em></li>
        <li><em><strong>Past 2&nbsp;months bank statements on associated account to be used.</strong></em></li>
</ul><p>Your merchant account will be operational within 1 business day.&nbsp;</p>

<p>&nbsp;</p>

<h3>How long does it take for a payment to be deposited?</h3>

<p>Funds will typically be deposited into the Payee\'\'s banking account 3-5&nbsp;business days after we begin processing the payment. This usually depends on your own bank.&nbsp;</p>

<p>&nbsp;</p>

<h3>What are the services offered?</h3>

<p><strong>Included Free in Monthly Fee: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</strong></p>

<p>Receive Credit Card and ACH Rent Payments Online &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>

<p>Payment History &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>

<p>Maintenance/Service Requests Online &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>

<p>Online Forum &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;</p>

<p>Upload Your Own Rental Applications and Contracts And Charge Fees</p>

<p>Save All Documentation&nbsp;</p>

<p>Upload And Send Receipts</p>

<p>Create Online Rental Application</p>

<p>Create Online Rental Application</p>

<p>Post Property Vacancies Online&nbsp;</p>

<p><strong>Pay as You Go:</strong></p>

<p>Full Credit, Eviction, and Criminal&nbsp;Screening - $15</p>

<p>E-signature per Form - $2.95</p>

<p>&nbsp;</p>

<h3>Can property landlords&nbsp;become members?</h3>

<p>Yes, landlords&nbsp;can become members too for a monthly fee of 11.95 per month.</p>

<p>&nbsp;</p>

<h3>Still have questions?</h3>

<p>Please submit any questions through the <a href="https://zoobdoo.com/contact" style="color:rgb(85,85,85);" title="Contact Zoobdoo">contact us</a> page or info@zoobdoo.com.&nbsp;</p>\' WHERE code = \'faq\';');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE static_pages SET content=\'<h3>What is Zoobdoo?</h3>

<p>Our service allows property&nbsp;managers to&nbsp;build their business by keeping things simple and affordable. Now property managers have real options.&nbsp;manage your rents, get background and&nbsp;<a href="https://zoobdoo.com/online-tenant-screening">credit checks for property managers</a>, create rental contracts along with receiving online digital signatures,&nbsp;<a href="https://zoobdoo.com/pay-rent-online">accept rent payments online</a>, send rental applications and collect fees, and even post&nbsp;<a href="https://zoobdoo.com/post-apartment-for-rent">apartments for rent</a>&nbsp;from the Zoobdoo website onto other public sites.&nbsp;</p>

<h3>&nbsp;</h3>

<h3>How do the transactions occur?</h3>

<p>The tenant submits payment through Zoobdoo tenant portal, payment processes through the Automatic Clearing House (ACH) or Credit Processor (CC), and payment is deposited directly in property managers account.&nbsp;</p>

<p>&nbsp;</p>

<h3>Price: How much does it cost?</h3>

<ul><li>Property Managers pay nothing!&nbsp;No setup or monthly fees! No contracts or obligations!&nbsp;</li>
        <li>Tenant pays&nbsp;a small $3.00 flat fee per ACH transaction.</li>
        <li>Tenant pays&nbsp;a 2.5% per Credit Card transaction. (Accept Visa, Mastercard and Discover)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
        <li>Application fee per applicant submission,&nbsp;$1.00.&nbsp;(Perspective Tenant pays during application process)</li>
        <li>Credit, background, and eviction check,&nbsp;$19.95.</li>
        <li>Posting properties online, FREE.</li>
</ul><p>&nbsp;</p>

<h3>Do I have to sign a contract?</h3>

<p>No, there is no long or short term contract or obligation. You can cancel at anytime.</p>

<p>&nbsp;</p>

<h3>Are there any hidden fees or obligations?</h3>

<p>There are no hidden fees.&nbsp;No contracts or obligations.</p>

<p>&nbsp;</p>

<h3>How do I start accepting tenant payments directly to my account?&nbsp;</h3>

<p>For your security we require the following documents be uploaded using the SendSafely secured and encrypted link.</p>

<ul><li><em><strong>A copy of a voided business bank account check for account to be used.</strong></em></li>
        <li><em><strong>Past 2&nbsp;months bank statements on associated account to be used.</strong></em></li>
</ul><p>Your merchant account will be operational within 1 business day.&nbsp;</p>

<p>&nbsp;</p>

<h3>How long does it take for a payment to be deposited?</h3>

<p>Funds will typically be deposited into the Payee\'\'s banking account 3-5&nbsp;business days after we begin processing the payment. This usually depends on your own bank.&nbsp;</p>

<p>&nbsp;</p>

<h3>What are the services offered?</h3>

<p><strong>Included Free in Monthly Fee: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</strong></p>

<p>Receive Credit Card and ACH Rent Payments Online &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>

<p>Payment History &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>

<p>Maintenance/Service Requests Online &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>

<p>Online Forum &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;</p>

<p>Upload Your Own Rental Applications and Contracts And Charge Fees</p>

<p>Save All Documentation&nbsp;</p>

<p>Upload And Send Receipts</p>

<p>Create Online Rental Application</p>

<p>Create Online Rental Application</p>

<p>Post Property Vacancies Online&nbsp;</p>

<p><strong>Pay as You Go:</strong></p>

<p>Full Credit, Eviction, and Criminal&nbsp;Screening - $19.95</p>

<p>E-signature per Form - $2.95</p>

<p>&nbsp;</p>

<h3>Can property landlords&nbsp;become members?</h3>

<p>Yes, landlords&nbsp;can become members too for a monthly fee of 11.95 per month.</p>

<p>&nbsp;</p>

<h3>Still have questions?</h3>

<p>Please submit any questions through the <a href="https://zoobdoo.com/contact" style="color:rgb(85,85,85);" title="Contact Zoobdoo">contact us</a> page or info@zoobdoo.com.&nbsp;</p>\' WHERE code = \'faq\';');
    }
}
