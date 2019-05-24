<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180110164015 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE stripe_subscription (id INT AUTO_INCREMENT NOT NULL, stripe_customer_id INT DEFAULT NULL, subscription_id VARCHAR(255) NOT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6F290B43708DC647 (stripe_customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stripe_subscription ADD CONSTRAINT FK_6F290B43708DC647 FOREIGN KEY (stripe_customer_id) REFERENCES stripe_customer (id)');
        $this->addSql('DROP TABLE user_unit_settings');
        $this->addSql('ALTER TABLE stripe_recurring_payment DROP FOREIGN KEY FK_C26CDE1E708DC647');
        $this->addSql('DROP INDEX IDX_C26CDE1E708DC647 ON stripe_recurring_payment');
        $this->addSql('ALTER TABLE stripe_recurring_payment ADD account_id INT DEFAULT NULL, ADD amount DOUBLE PRECISION DEFAULT NULL, ADD type ENUM(\'single\',\'recurring\'), ADD status ENUM(\'pending\', \'failure\', \'success\'), ADD start_payment_at DATE DEFAULT NULL, ADD next_payment_at DATE DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, DROP subscription_id, DROP created_at, DROP updated_at, DROP quantity, CHANGE stripe_customer_id customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stripe_recurring_payment ADD CONSTRAINT FK_C26CDE1E9395C3F3 FOREIGN KEY (customer_id) REFERENCES stripe_customer (id)');
        $this->addSql('ALTER TABLE stripe_recurring_payment ADD CONSTRAINT FK_C26CDE1E9B6B5FBA FOREIGN KEY (account_id) REFERENCES stripe_account (id)');
        $this->addSql('CREATE INDEX IDX_C26CDE1E9395C3F3 ON stripe_recurring_payment (customer_id)');
        $this->addSql('CREATE INDEX IDX_C26CDE1E9B6B5FBA ON stripe_recurring_payment (account_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_unit_settings (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, initial_quantity INT NOT NULL, quantity_per_unit INT NOT NULL, INDEX IDX_7EF5E707A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_unit_settings ADD CONSTRAINT FK_7EF5E707A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE stripe_subscription');
        $this->addSql('ALTER TABLE stripe_recurring_payment DROP FOREIGN KEY FK_C26CDE1E9395C3F3');
        $this->addSql('ALTER TABLE stripe_recurring_payment DROP FOREIGN KEY FK_C26CDE1E9B6B5FBA');
        $this->addSql('DROP INDEX IDX_C26CDE1E9395C3F3 ON stripe_recurring_payment');
        $this->addSql('DROP INDEX IDX_C26CDE1E9B6B5FBA ON stripe_recurring_payment');
        $this->addSql('ALTER TABLE stripe_recurring_payment ADD stripe_customer_id INT DEFAULT NULL, ADD subscription_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD quantity INT NOT NULL, DROP customer_id, DROP account_id, DROP amount, DROP type, DROP status, DROP start_payment_at, DROP next_payment_at, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE stripe_recurring_payment ADD CONSTRAINT FK_C26CDE1E708DC647 FOREIGN KEY (stripe_customer_id) REFERENCES stripe_customer (id)');
        $this->addSql('CREATE INDEX IDX_C26CDE1E708DC647 ON stripe_recurring_payment (stripe_customer_id)');
    }
}
