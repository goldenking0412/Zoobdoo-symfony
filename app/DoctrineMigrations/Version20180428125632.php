<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180428125632 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE properties_settings DROP FOREIGN KEY FK_A00479B1549213EC');
        $this->addSql('DROP INDEX UNIQ_A00479B1549213EC ON properties_settings');
        $this->addSql('ALTER TABLE properties_settings CHANGE property_id settings_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE properties_settings ADD CONSTRAINT FK_A00479B159949888 FOREIGN KEY (settings_id) REFERENCES properties (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A00479B159949888 ON properties_settings (settings_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE properties_settings DROP FOREIGN KEY FK_A00479B159949888');
        $this->addSql('DROP INDEX UNIQ_A00479B159949888 ON properties_settings');
        $this->addSql('ALTER TABLE properties_settings CHANGE settings_id property_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE properties_settings ADD CONSTRAINT FK_A00479B1549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A00479B1549213EC ON properties_settings (property_id)');
    }
}
