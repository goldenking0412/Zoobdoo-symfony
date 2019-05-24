<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180520180933 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE property_security_deposit (id INT AUTO_INCREMENT NOT NULL, not_want_security_deposit TINYINT(1) DEFAULT NULL, yes_want_security_deposit TINYINT(1) DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, refunded_amount DOUBLE PRECISION DEFAULT NULL, send_to_main_account TINYINT(1) DEFAULT NULL, add_bank_account TINYINT(1) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lease_out_history (id INT AUTO_INCREMENT NOT NULL, created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, property_data LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property_lease_out_history (property_id INT NOT NULL, lease_out_history INT NOT NULL, INDEX IDX_EDED7805549213EC (property_id), INDEX IDX_EDED78051421CA72 (lease_out_history), PRIMARY KEY(property_id, lease_out_history)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stripe_deposit_account (id INT AUTO_INCREMENT NOT NULL, account_id VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, line1 VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, business_name VARCHAR(255) DEFAULT NULL, business_tax_id VARCHAR(255) DEFAULT NULL, day_of_birth VARCHAR(255) DEFAULT NULL, month_of_birth VARCHAR(255) DEFAULT NULL, year_of_birth VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, ssn_last4 VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, tos_acceptance_date DATE DEFAULT NULL, tos_acceptance_ip VARCHAR(255) DEFAULT NULL, bank_account_id VARCHAR(255) DEFAULT NULL, bank_name VARCHAR(255) DEFAULT NULL, account_holder_name VARCHAR(255) DEFAULT NULL, routing_number VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE property_lease_out_history ADD CONSTRAINT FK_EDED7805549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('ALTER TABLE property_lease_out_history ADD CONSTRAINT FK_EDED78051421CA72 FOREIGN KEY (lease_out_history) REFERENCES lease_out_history (id)');
        $this->addSql('ALTER TABLE pro_requests CHANGE status status ENUM(\'in_process\', \'approved\', \'payment_ok\', \'payment_error\', \'canceled\') NOT NULL
                    DEFAULT \'in_process\'');
        $this->addSql('ALTER TABLE erp_user_fee RENAME INDEX idx_e54e5729a76ed395 TO IDX_2EB5E72DA76ED395');
        $this->addSql('ALTER TABLE rent_payment_balance DROP INDEX IDX_92582F77A76ED395, ADD UNIQUE INDEX UNIQ_92582F77A76ED395 (user_id)');
        $this->addSql('ALTER TABLE users CHANGE status status ENUM(\'pending\',\'active\',\'not_confirmed\',\'disabled\',\'rejected\',\'deleted\') DEFAULT \'not_confirmed\', CHANGE is_private_paysimple is_private_paysimple TINYINT(1) NOT NULL, CHANGE paysimple_api_secret_key paysimple_api_secret_key VARCHAR(255) DEFAULT NULL, CHANGE is_property_counter_free is_property_counter_free TINYINT(1) NOT NULL, CHANGE is_application_form_counter_free is_application_form_counter_free TINYINT(1) NOT NULL, CHANGE is_active_monthly_fee is_active_monthly_fee TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user_documents CHANGE status status ENUM(\'Sent\',\'Completed\',\'Pending\') DEFAULT \'Pending\'');
        $this->addSql('ALTER TABLE fees_options CHANGE default_email default_email VARCHAR(255) NOT NULL, CHANGE smart_move_enable smart_move_enable TINYINT(1) NOT NULL, CHANGE cc_transaction_fee cc_transaction_fee DOUBLE PRECISION DEFAULT \'1\' NOT NULL, CHANGE application_form_anonymous_fee application_form_anonymous_fee DOUBLE PRECISION DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE property_repost_requests CHANGE status status ENUM(\'new\',\'accepted\', \'rejected\') NOT NULL DEFAULT \'new\', CHANGE note note VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE application_fields CHANGE section_id section_id INT DEFAULT NULL, CHANGE type type ENUM(\'file\',\'text\',\'checkbox\',\'radio\') NOT NULL DEFAULT \'text\'');
        $this->addSql('ALTER TABLE scheduled_rent_payment RENAME INDEX idx_c26cde1e9395c3f3 TO IDX_5A607FEC9395C3F3');
        $this->addSql('ALTER TABLE scheduled_rent_payment RENAME INDEX idx_c26cde1e9b6b5fba TO IDX_5A607FEC9B6B5FBA');
        $this->addSql('ALTER TABLE properties ADD deposit_id INT DEFAULT NULL, ADD deposit_account_id INT DEFAULT NULL, CHANGE status status ENUM(\'available\',\'rented\', \'draft\', \'deleted\') DEFAULT \'draft\'');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C79815E4B1 FOREIGN KEY (deposit_id) REFERENCES property_security_deposit (id)');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C76E60BC73 FOREIGN KEY (deposit_account_id) REFERENCES stripe_deposit_account (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87C331C79815E4B1 ON properties (deposit_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87C331C76E60BC73 ON properties (deposit_account_id)');
        $this->addSql('ALTER TABLE contract_forms RENAME INDEX uniq_property TO UNIQ_BE3E87D3549213EC');
        $this->addSql('ALTER TABLE contract_sections RENAME INDEX idx_form TO IDX_BE2158675FF69B7D');
        $this->addSql('ALTER TABLE homepage_slides CHANGE image_id image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ps_recurring_payment CHANGE subscription_type subscription_type ENUM(\'cc\',\'ba\') NOT NULL DEFAULT \'cc\', CHANGE status status ENUM(\'Active\',\'Expired\',\'Suspended\', \'PauseUntil\') NOT NULL DEFAULT \'Active\', CHANGE type type ENUM(\'one\',\'recurring\') NOT NULL DEFAULT \'recurring\'');
        $this->addSql('ALTER TABLE ps_deferred_payments CHANGE status status ENUM(\'Pending\', \'Posted\', \'Settled\', \'Authorized\', \'Failed\') NOT NULL DEFAULT \'Pending\'');
        $this->addSql('ALTER TABLE ps_customers CHANGE primary_type primary_type ENUM(\'cc\',\'ba\')');
        $this->addSql('ALTER TABLE ps_history CHANGE status status ENUM(\'success\',\'error\',\'pending\') NOT NULL DEFAULT \'success\', CHANGE transfer_date transfer_date DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE properties DROP FOREIGN KEY FK_87C331C79815E4B1');
        $this->addSql('ALTER TABLE property_lease_out_history DROP FOREIGN KEY FK_EDED78051421CA72');
        $this->addSql('ALTER TABLE properties DROP FOREIGN KEY FK_87C331C76E60BC73');
        $this->addSql('DROP TABLE property_security_deposit');
        $this->addSql('DROP TABLE lease_out_history');
        $this->addSql('DROP TABLE property_lease_out_history');
        $this->addSql('DROP TABLE stripe_deposit_account');
        $this->addSql('ALTER TABLE application_fields CHANGE section_id section_id INT NOT NULL, CHANGE type type VARCHAR(255) DEFAULT \'text\' NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE contract_forms RENAME INDEX uniq_be3e87d3549213ec TO UNIQ_PROPERTY');
        $this->addSql('ALTER TABLE contract_sections RENAME INDEX idx_be2158675ff69b7d TO IDX_FORM');
        $this->addSql('ALTER TABLE erp_user_fee RENAME INDEX idx_2eb5e72da76ed395 TO IDX_E54E5729A76ED395');
        $this->addSql('ALTER TABLE fees_options CHANGE default_email default_email VARCHAR(255) DEFAULT \'info@erent.com\' NOT NULL COLLATE utf8_unicode_ci, CHANGE cc_transaction_fee cc_transaction_fee DOUBLE PRECISION DEFAULT \'3\' NOT NULL, CHANGE application_form_anonymous_fee application_form_anonymous_fee DOUBLE PRECISION DEFAULT \'0.01\' NOT NULL, CHANGE smart_move_enable smart_move_enable TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE homepage_slides CHANGE image_id image_id INT NOT NULL');
        $this->addSql('ALTER TABLE pro_requests CHANGE status status VARCHAR(255) DEFAULT \'in_process\' NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX UNIQ_87C331C79815E4B1 ON properties');
        $this->addSql('DROP INDEX UNIQ_87C331C76E60BC73 ON properties');
        $this->addSql('ALTER TABLE properties DROP deposit_id, DROP deposit_account_id, CHANGE status status VARCHAR(255) DEFAULT \'draft\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE property_repost_requests CHANGE status status VARCHAR(255) DEFAULT \'new\' NOT NULL COLLATE utf8_unicode_ci, CHANGE note note VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE ps_customers CHANGE primary_type primary_type VARCHAR(255) DEFAULT \'cc\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE ps_deferred_payments CHANGE status status VARCHAR(255) DEFAULT \'Pending\' NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE ps_history CHANGE status status VARCHAR(255) DEFAULT \'success\' NOT NULL COLLATE utf8_unicode_ci, CHANGE transfer_date transfer_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE ps_recurring_payment CHANGE subscription_type subscription_type VARCHAR(255) DEFAULT \'cc\' NOT NULL COLLATE utf8_unicode_ci, CHANGE status status VARCHAR(255) DEFAULT \'Active\' NOT NULL COLLATE utf8_unicode_ci, CHANGE type type VARCHAR(255) DEFAULT \'recurring\' NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE rent_payment_balance DROP INDEX UNIQ_92582F77A76ED395, ADD INDEX IDX_92582F77A76ED395 (user_id)');
        $this->addSql('ALTER TABLE scheduled_rent_payment RENAME INDEX idx_5a607fec9395c3f3 TO IDX_C26CDE1E9395C3F3');
        $this->addSql('ALTER TABLE scheduled_rent_payment RENAME INDEX idx_5a607fec9b6b5fba TO IDX_C26CDE1E9B6B5FBA');
        $this->addSql('ALTER TABLE user_documents CHANGE status status VARCHAR(255) DEFAULT \'Pending\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE users CHANGE is_private_paysimple is_private_paysimple TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE paysimple_api_secret_key paysimple_api_secret_key TEXT DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE status status VARCHAR(255) DEFAULT \'not_confirmed\' COLLATE utf8_unicode_ci, CHANGE is_property_counter_free is_property_counter_free TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE is_application_form_counter_free is_application_form_counter_free TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE is_active_monthly_fee is_active_monthly_fee TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
