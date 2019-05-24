<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180831180818 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ps_deferred_payments DROP FOREIGN KEY FK_14DCBD178A176DAA');
        $this->addSql('ALTER TABLE ps_recurring_payment DROP FOREIGN KEY FK_5EF8F038A176DAA');
        $this->addSql('DROP TABLE ps_customers');
        $this->addSql('DROP TABLE ps_deferred_payments');
        $this->addSql('DROP TABLE ps_history');
        $this->addSql('DROP TABLE ps_recurring_payment');
        $this->addSql('ALTER TABLE user_documents CHANGE status status ENUM(\'Sent\',\'Completed\',\'Pending\') DEFAULT \'Sent\', CHANGE num_of_signatures num_of_signatures INT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE stripe_customer DROP INDEX UNIQ_DC7E523AA76ED395, ADD INDEX IDX_DC7E523AA76ED395 (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ps_customers (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, customer_id INT NOT NULL, cc_id INT DEFAULT NULL, ba_id INT DEFAULT NULL, created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, primary_type VARCHAR(2) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_F41AF423A76ED395 (user_id), INDEX customer_idx (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ps_deferred_payments (id INT AUTO_INCREMENT NOT NULL, ps_customer_id INT DEFAULT NULL, account_id INT NOT NULL, transaction_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, payment_date DATETIME NOT NULL, status VARCHAR(16) DEFAULT NULL COLLATE utf8_unicode_ci, maked_date DATETIME DEFAULT NULL, created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, is_canceled TINYINT(1) NOT NULL, allowance DOUBLE PRECISION DEFAULT NULL, INDEX IDX_14DCBD178A176DAA (ps_customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ps_history (id INT AUTO_INCREMENT NOT NULL, property_id INT NOT NULL, user_id INT DEFAULT NULL, paymentDate DATETIME NOT NULL, amount DOUBLE PRECISION NOT NULL, status VARCHAR(16) DEFAULT NULL COLLATE utf8_unicode_ci, notes LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, transfer_date DATETIME NOT NULL, INDEX IDX_EA3D5CA1549213EC (property_id), INDEX IDX_EA3D5CA1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ps_recurring_payment (id INT AUTO_INCREMENT NOT NULL, ps_customer_id INT DEFAULT NULL, recurring_id INT NOT NULL, monthly_amount DOUBLE PRECISION DEFAULT NULL, subscription_type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, account_id INT NOT NULL, start_date DATETIME NOT NULL, next_date DATETIME NOT NULL, status VARCHAR(16) DEFAULT NULL COLLATE utf8_unicode_ci, created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, last_checked_date DATETIME DEFAULT NULL, allowance DOUBLE PRECISION DEFAULT NULL, INDEX IDX_5EF8F038A176DAA (ps_customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ps_customers ADD CONSTRAINT FK_F41AF423A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE ps_deferred_payments ADD CONSTRAINT FK_14DCBD178A176DAA FOREIGN KEY (ps_customer_id) REFERENCES ps_customers (id)');
        $this->addSql('ALTER TABLE ps_history ADD CONSTRAINT FK_EA3D5CA1549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('ALTER TABLE ps_history ADD CONSTRAINT FK_EA3D5CA1A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE ps_recurring_payment ADD CONSTRAINT FK_5EF8F038A176DAA FOREIGN KEY (ps_customer_id) REFERENCES ps_customers (id)');
        $this->addSql('ALTER TABLE stripe_customer DROP INDEX IDX_DC7E523AA76ED395, ADD UNIQUE INDEX UNIQ_DC7E523AA76ED395 (user_id)');
        $this->addSql('ALTER TABLE user_documents CHANGE status status VARCHAR(255) DEFAULT \'Sent\' COLLATE utf8_unicode_ci, CHANGE num_of_signatures num_of_signatures INT DEFAULT 0 NOT NULL');
    }
}
