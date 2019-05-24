<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180126092354 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stripe_invoices DROP FOREIGN KEY FK_3768F3A67E3C61F9');
        $this->addSql('DROP INDEX IDX_3768F3A67E3C61F9 ON stripe_invoices');
        $this->addSql('ALTER TABLE stripe_invoices ADD customer_id INT DEFAULT NULL, CHANGE owner_id account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stripe_invoices ADD CONSTRAINT FK_3768F3A69B6B5FBA FOREIGN KEY (account_id) REFERENCES stripe_account (id)');
        $this->addSql('ALTER TABLE stripe_invoices ADD CONSTRAINT FK_3768F3A69395C3F3 FOREIGN KEY (customer_id) REFERENCES stripe_customer (id)');
        $this->addSql('CREATE INDEX IDX_3768F3A69B6B5FBA ON stripe_invoices (account_id)');
        $this->addSql('CREATE INDEX IDX_3768F3A69395C3F3 ON stripe_invoices (customer_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stripe_invoices DROP FOREIGN KEY FK_3768F3A69B6B5FBA');
        $this->addSql('ALTER TABLE stripe_invoices DROP FOREIGN KEY FK_3768F3A69395C3F3');
        $this->addSql('DROP INDEX IDX_3768F3A69B6B5FBA ON stripe_invoices');
        $this->addSql('DROP INDEX IDX_3768F3A69395C3F3 ON stripe_invoices');
        $this->addSql('ALTER TABLE stripe_invoices ADD owner_id INT DEFAULT NULL, DROP account_id, DROP customer_id');
        $this->addSql('ALTER TABLE stripe_invoices ADD CONSTRAINT FK_3768F3A67E3C61F9 FOREIGN KEY (owner_id) REFERENCES stripe_account (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_3768F3A67E3C61F9 ON stripe_invoices (owner_id)');
    }
}
