<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180118085753 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql('ALTER TABLE stripe_account  ADD line1 VARCHAR(255) DEFAULT NULL, ADD postal_code VARCHAR(255) DEFAULT NULL, ADD state VARCHAR(255) DEFAULT NULL, ADD business_name VARCHAR(255) DEFAULT NULL, ADD business_tax_id VARCHAR(255) DEFAULT NULL, ADD date_of_birth DATE DEFAULT NULL, ADD first_name VARCHAR(255) DEFAULT NULL, ADD last_name VARCHAR(255) DEFAULT NULL, ADD ssn_last4 VARCHAR(255) DEFAULT NULL, ADD type VARCHAR(255) DEFAULT NULL, ADD tos_acceptance_date DATE DEFAULT NULL, ADD tos_acceptance_ip VARCHAR(255) DEFAULT NULL');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stripe_account DROP FOREIGN KEY FK_52F1675E8BAC62AF');
        $this->addSql('DROP INDEX UNIQ_52F1675E8BAC62AF ON stripe_account');
        $this->addSql('ALTER TABLE stripe_account DROP line1, DROP postal_code, DROP state, DROP business_name, DROP business_tax_id, DROP date_of_birth, DROP first_name, DROP last_name, DROP ssn_last4, DROP type, DROP tos_acceptance_date, DROP tos_acceptance_ip');
    }
}
