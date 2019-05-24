<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171206134325 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE properties_settings (id INT AUTO_INCREMENT NOT NULL, property_id INT DEFAULT NULL, payment_acceptance_date_from DATE NOT NULL, payment_acceptance_date_to DATE NOT NULL, payment_amount DOUBLE PRECISION DEFAULT NULL, is_allow_partial_payments TINYINT(1) NOT NULL, is_allow_credit_card_payments TINYINT(1) NOT NULL, is_allow_auto_draft TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_A00479B1549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE properties_settings ADD CONSTRAINT FK_A00479B1549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');

        $this->addSql('ALTER TABLE properties ADD settings_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C759949888 FOREIGN KEY (settings_id) REFERENCES properties_settings (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87C331C759949888 ON properties (settings_id)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE properties DROP FOREIGN KEY FK_87C331C759949888');
        $this->addSql('DROP TABLE properties_settings');
        $this->addSql('DROP INDEX UNIQ_87C331C759949888 ON properties');
        $this->addSql('ALTER TABLE properties DROP settings_id');



    }
}
