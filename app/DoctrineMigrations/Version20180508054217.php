<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180508054217 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE properties_settings ADD move_in_date DATE DEFAULT NULL, ADD lease_end DATE DEFAULT NULL, ADD term_lease TINYINT(1) DEFAULT NULL, ADD at_will TINYINT(1) DEFAULT NULL, CHANGE payment_amount payment_amount DOUBLE PRECISION DEFAULT NULL, CHANGE is_allow_partial_payments is_allow_partial_payments TINYINT(1) DEFAULT NULL, CHANGE is_allow_credit_card_payments is_allow_credit_card_payments TINYINT(1) DEFAULT NULL, CHANGE is_allow_auto_draft is_allow_auto_draft TINYINT(1) DEFAULT NULL, CHANGE day_until_due day_until_due INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invited_users ADD first_name VARCHAR(255) DEFAULT NULL, ADD last_name VARCHAR(255) DEFAULT NULL, ADD age INT DEFAULT NULL, ADD birthdate DATE DEFAULT NULL, CHANGE property_id property_id INT DEFAULT NULL, CHANGE invited_email invited_email VARCHAR(255) DEFAULT NULL, CHANGE invited_code invited_code VARCHAR(50) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE invited_users DROP first_name, DROP last_name, DROP age, DROP birthdate, CHANGE property_id property_id INT DEFAULT NULL, CHANGE invited_email invited_email VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE invited_code invited_code VARCHAR(50) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE properties_settings DROP move_in_date, DROP lease_end, DROP term_lease, DROP at_will, CHANGE day_until_due day_until_due INT NOT NULL, CHANGE payment_amount payment_amount DOUBLE PRECISION DEFAULT \'NULL\', CHANGE is_allow_partial_payments is_allow_partial_payments TINYINT(1) NOT NULL, CHANGE is_allow_credit_card_payments is_allow_credit_card_payments TINYINT(1) NOT NULL, CHANGE is_allow_auto_draft is_allow_auto_draft TINYINT(1) NOT NULL');
    }
}
