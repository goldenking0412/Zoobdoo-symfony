<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180112091858 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql('CREATE TABLE stripe_transactions (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, amount INTEGER NOT NULL, currency VARCHAR(255) NOT NULL, created DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_264775DCA76ED395 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stripe_invoices (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, amount INTEGER NOT NULL, created DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3768F3A67E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE stripe_transactions ADD CONSTRAINT FK_264775DCA76ED395 FOREIGN KEY (owner_id) REFERENCES stripe_account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stripe_invoices ADD CONSTRAINT FK_3768F3A67E3C61F9 FOREIGN KEY (owner_id) REFERENCES stripe_account (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE stripe_transactions');
        $this->addSql('DROP TABLE stripe_invoices');
    }
}
