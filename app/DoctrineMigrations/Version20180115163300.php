<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180115163300 extends AbstractMigration
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
        $this->addSql('ALTER TABLE properties_settings DROP property_id');
        $this->addSql('ALTER TABLE stripe_account DROP INDEX IDX_52F1675EA76ED395, ADD UNIQUE INDEX UNIQ_52F1675EA76ED395 (user_id)');
        $this->addSql('ALTER TABLE stripe_account DROP FOREIGN KEY FK_52F1675EA76ED395');
        $this->addSql('ALTER TABLE stripe_account ADD CONSTRAINT FK_52F1675EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE stripe_customer DROP INDEX IDX_DC7E523AA76ED395, ADD UNIQUE INDEX UNIQ_DC7E523AA76ED395 (user_id)');
        $this->addSql('ALTER TABLE stripe_transactions RENAME INDEX idx_264775dca76ed395 TO IDX_264775DC7E3C61F9');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE properties_settings ADD property_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE properties_settings ADD CONSTRAINT FK_A00479B1549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A00479B1549213EC ON properties_settings (property_id)');
        $this->addSql('ALTER TABLE stripe_account DROP INDEX UNIQ_52F1675EA76ED395, ADD INDEX IDX_52F1675EA76ED395 (user_id)');
        $this->addSql('ALTER TABLE stripe_account DROP FOREIGN KEY FK_52F1675EA76ED395');
        $this->addSql('ALTER TABLE stripe_account ADD CONSTRAINT FK_52F1675EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stripe_customer DROP INDEX UNIQ_DC7E523AA76ED395, ADD INDEX IDX_DC7E523AA76ED395 (user_id)');
        $this->addSql('ALTER TABLE stripe_transactions RENAME INDEX idx_264775dc7e3c61f9 TO IDX_264775DCA76ED395');


    }
}
