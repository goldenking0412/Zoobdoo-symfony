<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151119162154 extends AbstractMigration
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

        $this->addSql('ALTER TABLE ps_recurring_payment ADD last_checked_date DATETIME DEFAULT NULL');
        $this->addSql(
            'ALTER TABLE ps_recurring_payment
            CHANGE status status ENUM(\'Active\',\'Expired\',\'Suspended\', \'PauseUntil\') NOT NULL DEFAULT \'Active\''
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

        $this->addSql('ALTER TABLE ps_recurring_payment DROP last_checled_date');
        $this->addSql(
            'ALTER TABLE ps_recurring_payment
            CHANGE status status VARCHAR(255) DEFAULT \'pending\' NOT NULL COLLATE utf8_unicode_ci'
        );
    }
}
