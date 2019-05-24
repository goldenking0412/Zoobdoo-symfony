<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180423110345 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE properties ADD paid_date DATETIME NULL');
        $this->addSql('ALTER TABLE stripe_transactions ADD property_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stripe_transactions ADD CONSTRAINT FK_264775DC549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_264775DC549213EC ON stripe_transactions (property_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE properties DROP paid_date');
        $this->addSql('ALTER TABLE stripe_transactions DROP FOREIGN KEY FK_264775DC549213EC');
        $this->addSql('DROP INDEX IDX_264775DC549213EC ON stripe_transactions');
        $this->addSql('ALTER TABLE stripe_transactions DROP property_id');
    }
}
