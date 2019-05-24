<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180413142422 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE pro_requests CHANGE status status VARCHAR(32) NULL DEFAULT \'in_process\';');
        $this->addSql('ALTER TABLE users CHANGE status status VARCHAR(32) NULL DEFAULT \'not_confirmed\';');
        $this->addSql('ALTER TABLE user_documents CHANGE status status VARCHAR(32) NULL DEFAULT \'Pending\';');
        $this->addSql('ALTER TABLE application_fields CHANGE type type VARCHAR(16) NULL DEFAULT \'text\';');
        $this->addSql('ALTER TABLE properties CHANGE status status VARCHAR(16) NULL DEFAULT \'draft\';');
        $this->addSql('ALTER TABLE property_repost_requests CHANGE status status VARCHAR(16) NULL DEFAULT \'new\', CHANGE note note VARCHAR(255) DEFAULT NULL;');
        $this->addSql('ALTER TABLE ps_customers CHANGE primary_type primary_type VARCHAR(2) DEFAULT NULL;');
        $this->addSql('ALTER TABLE ps_deferred_payments CHANGE status status VARCHAR(16) DEFAULT \'Pending\';');
        $this->addSql('ALTER TABLE ps_history CHANGE status status VARCHAR(16) NULL DEFAULT \'success\';');
        $this->addSql('ALTER TABLE ps_recurring_payment CHANGE subscription_type subscription_type VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(16) NULL DEFAULT \'Active\', CHANGE type type VARCHAR(255) DEFAULT NULL;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE pro_requests CHANGE status status ENUM(\'in_process\', \'approved\', \'payment_ok\', \'payment_error\', \'canceled\') NOT NULL DEFAULT \'in_process\';');
        $this->addSql('ALTER TABLE users CHANGE status status ENUM(\'pending\',\'active\',\'not_confirmed\',\'disabled\',\'rejected\',\'deleted\') DEFAULT \'not_confirmed\';');
        $this->addSql('ALTER TABLE user_documents CHANGE status status ENUM(\'Sent\',\'Completed\',\'Pending\') DEFAULT \'Pending\';');
        $this->addSql('ALTER TABLE application_fields CHANGE type type ENUM(\'file\',\'text\',\'checkbox\',\'radio\') NOT NULL DEFAULT \'text\';');
        $this->addSql('ALTER TABLE properties CHANGE status status ENUM(\'available\',\'rented\', \'draft\', \'deleted\') DEFAULT \'draft\';');
        $this->addSql('ALTER TABLE property_repost_requests CHANGE status status ENUM(\'new\',\'accepted\', \'rejected\') NOT NULL DEFAULT \'new\';');
        $this->addSql('ALTER TABLE ps_customers CHANGE primary_type primary_type ENUM(\'cc\',\'ba\');');
        $this->addSql('ALTER TABLE ps_deferred_payments CHANGE status status ENUM(\'Pending\', \'Posted\', \'Settled\', \'Authorized\', \'Failed\') NOT NULL DEFAULT \'Pending\';');
        $this->addSql('ALTER TABLE ps_history CHANGE status status ENUM(\'success\',\'error\',\'pending\') NOT NULL DEFAULT \'success\';');
        $this->addSql('ALTER TABLE ps_recurring_payment CHANGE status status ENUM(\'Active\',\'Expired\',\'Suspended\', \'PauseUntil\') NOT NULL DEFAULT \'Active\';');
    }
}
