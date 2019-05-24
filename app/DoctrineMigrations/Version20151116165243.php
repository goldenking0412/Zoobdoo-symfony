<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151116165243 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'ALTER TABLE ps_recurring_payment
            CHANGE status status ENUM(\'active\',\'pending\',\'suspend\') NOT NULL DEFAULT \'pending\''
        );

        $this->addSql(
            'ALTER TABLE ps_recurring_payment ADD type ENUM(\'one\',\'recurring\') NOT NULL DEFAULT \'recurring\''
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql(
            'ALTER TABLE ps_recurring_payment
            CHANGE status status VARCHAR(255) DEFAULT \'pending\' NOT NULL COLLATE utf8_unicode_ci'
        );

        $this->addSql('ALTER TABLE ps_recurring_payment DROP type');
    }
}
