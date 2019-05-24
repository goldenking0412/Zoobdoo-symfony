<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180125165214 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql('ALTER TABLE stripe_transactions DROP FOREIGN KEY FK_264775DCA76ED395');
        $this->addSql('DROP INDEX IDX_264775DC7E3C61F9 ON stripe_transactions');
        $this->addSql('ALTER TABLE stripe_transactions ADD customer_id INT DEFAULT NULL, CHANGE owner_id account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stripe_transactions ADD CONSTRAINT FK_264775DC9B6B5FBA FOREIGN KEY (account_id) REFERENCES stripe_account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stripe_transactions ADD CONSTRAINT FK_264775DC9395C3F3 FOREIGN KEY (customer_id) REFERENCES stripe_customer (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_264775DC9B6B5FBA ON stripe_transactions (account_id)');
        $this->addSql('CREATE INDEX IDX_264775DC9395C3F3 ON stripe_transactions (customer_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stripe_transactions DROP FOREIGN KEY FK_264775DC9B6B5FBA');
        $this->addSql('ALTER TABLE stripe_transactions DROP FOREIGN KEY FK_264775DC9395C3F3');
        $this->addSql('DROP INDEX IDX_264775DC9B6B5FBA ON stripe_transactions');
        $this->addSql('DROP INDEX IDX_264775DC9395C3F3 ON stripe_transactions');
        $this->addSql('ALTER TABLE stripe_transactions ADD owner_id INT DEFAULT NULL, DROP account_id, DROP customer_id');
        $this->addSql('ALTER TABLE stripe_transactions ADD CONSTRAINT FK_264775DCA76ED395 FOREIGN KEY (owner_id) REFERENCES stripe_account (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_264775DC7E3C61F9 ON stripe_transactions (owner_id)');
    }
}
