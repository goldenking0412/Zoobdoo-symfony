<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180426094259 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE charges ADD transaction_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE charges ADD CONSTRAINT FK_3AEF501A2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES stripe_transactions (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AEF501A2FC0CB0F ON charges (transaction_id)');
        $this->addSql('ALTER TABLE stripe_transactions DROP FOREIGN KEY FK_264775DC55284914');
        $this->addSql('DROP INDEX UNIQ_264775DC55284914 ON stripe_transactions');
        $this->addSql('ALTER TABLE stripe_transactions CHANGE charge_id transaction_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE stripe_transactions ADD CONSTRAINT FK_264775DC2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES charges (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_264775DC2FC0CB0F ON stripe_transactions (transaction_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE charges DROP FOREIGN KEY FK_3AEF501A2FC0CB0F');
        $this->addSql('DROP INDEX UNIQ_3AEF501A2FC0CB0F ON charges');
        $this->addSql('ALTER TABLE charges DROP transaction_id');
        $this->addSql('ALTER TABLE stripe_transactions DROP FOREIGN KEY FK_264775DC2FC0CB0F');
        $this->addSql('DROP INDEX UNIQ_264775DC2FC0CB0F ON stripe_transactions');
        $this->addSql('ALTER TABLE stripe_transactions CHANGE transaction_id charge_id CHAR(36) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE stripe_transactions ADD CONSTRAINT FK_264775DC55284914 FOREIGN KEY (charge_id) REFERENCES charges (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_264775DC55284914 ON stripe_transactions (charge_id)');
    }
}
