<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180116124817 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE properties_settings CHANGE is_allow_partial_payments is_allow_partial_payments TINYINT(1) DEFAULT NULL, CHANGE is_allow_credit_card_payments is_allow_credit_card_payments TINYINT(1) DEFAULT NULL, CHANGE is_allow_auto_draft is_allow_auto_draft TINYINT(1) DEFAULT NULL, CHANGE day_until_due day_until_due INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE properties_settings CHANGE day_until_due day_until_due INT NOT NULL, CHANGE is_allow_partial_payments is_allow_partial_payments TINYINT(1) NOT NULL, CHANGE is_allow_credit_card_payments is_allow_credit_card_payments TINYINT(1) NOT NULL, CHANGE is_allow_auto_draft is_allow_auto_draft TINYINT(1) NOT NULL');
    }
}
