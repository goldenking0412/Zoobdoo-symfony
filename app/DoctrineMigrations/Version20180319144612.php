<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180319144612 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE stripe_transactions ADD charge_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\';');
        $this->addSql('ALTER TABLE stripe_transactions ADD CONSTRAINT FK_264775DC55284914 FOREIGN KEY (charge_id) REFERENCES charges (id);');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_264775DC55284914 ON stripe_transactions (charge_id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE stripe_transactions DROP FOREIGN KEY FK_264775DC55284914;');
        $this->addSql('DROP INDEX UNIQ_264775DC55284914 ON stripe_transactions;');
        $this->addSql('ALTER TABLE stripe_transactions DROP charge_id;');
    }
}
