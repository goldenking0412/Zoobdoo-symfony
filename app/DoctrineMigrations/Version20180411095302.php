<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;


class Version20180411095302 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE homepage_content SET service_body=\'<div class="row"><div class="aboutus_title">Are you a property manager or landlord looking to streamline your process? Perhaps you&#39;re a tenant who wishes it was easier to pay rent?</div><p>Sound familiar? <a href="/register">That&#39;s why we created Zoobdoo.</a> So thet everything a property manager, landlord, or tenant needs can be found in one place - in a simple, easy-to-navigate platform.</p><p><a href="#features">Check out the features below.</a></p></div>\';');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE homepage_content SET service_body=\'<div class="row"><div class="aboutus_title">Are you a property manager or landlord looking to streamline your process? Perhaps you&#39;re a tenant who wishes it was easier to pay rent?</div><p>Sound familiar? <a href="#">That&#39;s why we created Zoobdoo.</a> So thet everything a property manager, landlord, or tenant needs can be found in one place - in a simple, easy-to-navigate platform.</p><p><a href="#features">Check out the features below.</a></p></div>\';');
    }
}
