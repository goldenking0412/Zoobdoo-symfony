<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180412071523 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE static_pages SET content=\'<h3>Zoobdoo  is an online property management tool for property managers and tenants, and we love making everything easier. </h3>

<p>It’s what we do. It’s who we are. As a team of experienced professionals from many backgrounds, including realty and property management, we aspire for the online property management lifestyle: a place where property managers can effectively and affordably manage more properties with ease.  </p>

<p>Our goal is to create work that is honest and transparent. Solutions that are simple to understand, quick, and give managers the ability to manage far more properties then they are now. Passion and creativity drives us. We’re willing to go the distance for our property managers. We want them to walk away growing their business while saving more money, and we will do just about anything to get them there. </p>

<p>You can manage your rents, get background and <a href="https://zoobdoo.com/online-tenant-screening">credit checks for managers </a>, create rental contracts along with receiving online digital signatures, <a href="https://zoobdoo.com/pay-rent-online">accept rent payments online</a>, send rental applications and collect fees, and even post <a href="https://zoobdoo.com/post-apartment-for-rent">apartments for rent</a> from the Zoobdoo website onto other public sites. </p>

<p>Let’s us help you grow your business! Drop us an email now at <a href="mailto:info@zoobdoo.com">info@zoobdoo.com</a>, and let’s get started! </p>

<ul><li>• <a href="https://zoobdoo.com/manager-features" style="color:#555;" title="Managers">Manager Features</a></li>
	<li>• <a href="https://zoobdoo.com/tenant-features" style="color:#555;" title="Tenants">Tenant Features</a></li>
	<li>• <a href="https://zoobdoo.com/contact" style="color:#555;" title="Contact Zoobdoo">Contact us</a></li>
</ul>\' WHERE code = \'about\';');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE static_pages SET content=\'<h3>Zoobdoo  is an online property management tool for property managers and tenants, and we love making everything easier. </h3>

<p>It’s what we do. It’s who we are. As a team of experienced professionals from many backgrounds, including realty and property management, we aspire for the online property management lifestyle: a place where property managers can effectively and affordably manage more properties with ease.  </p>

<p>Our goal is to create work that is honest and transparent. Solutions that are simple to understand, quick, and give managers the ability to manage far more properties then they are now. Passion and creativity drives us. We’re willing to go the distance for our property managers. We want them to walk away growing their business while saving more money, and we will do just about anything to get them there. </p>

<p>You can manage your rents, get background and <a href="https://zoobdoo.com/online-tenant-screening">credit checks for managers </a>, create rental contracts along with receiving online digital signatures, <a href="https://zoobdoo.com/pay-rent-online">accept rent payments online</a>, send rental applications and collect fees, and even post <a href="https://zoobdoo.com/post-apartment-for-rent">apartments for rent</a> from the Zoobdoo website onto other public sites. </p>

<p>Let’s us help you grow your business! Drop us an email now at <a href="mailto:info@zoobdoo.com">info@zoobdoo.com</a>, and let’s get started! </p>

<ul><li>• <a href="https://zoobdoo.com/landlord-features" style="color:#555;" title="Landlords">Manager Features</a></li>
	<li>• <a href="https://zoobdoo.com/tenant-features" style="color:#555;" title="Tenants">Tenant Features</a></li>
	<li>• <a href="https://zoobdoo.com/contact" style="color:#555;" title="Contact Zoobdoo">Contact us</a></li>
</ul\' WHERE code = \'about\';');



    }
}
