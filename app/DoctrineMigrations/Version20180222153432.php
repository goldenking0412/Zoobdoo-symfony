<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180222153432 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rent_payment_balance (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, balance INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_92582F77A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE late_rent_payment (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, amount DECIMAL(15, 2) NOT NULL, type VARCHAR(255) NOT NULL, is_paid TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E54E5729A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rent_payment_transactions (rent_payment_id INT NOT NULL, transaction_id INT NOT NULL, INDEX IDX_A1D92E96AE2107D4 (rent_payment_id), UNIQUE INDEX UNIQ_A1D92E962FC0CB0F (transaction_id), PRIMARY KEY(rent_payment_id, transaction_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE rent_payment_balance ADD CONSTRAINT FK_92582F77A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE late_rent_payment ADD CONSTRAINT FK_E54E5729A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rent_payment_transactions ADD CONSTRAINT FK_A1D92E96AE2107D4 FOREIGN KEY (rent_payment_id) REFERENCES late_rent_payment (id)');
        $this->addSql('ALTER TABLE rent_payment_transactions ADD CONSTRAINT FK_A1D92E962FC0CB0F FOREIGN KEY (transaction_id) REFERENCES stripe_transactions (id)');
        $this->addSql('ALTER TABLE users ADD is_allow_rent_payment TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE stripe_transactions ADD metadata LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('RENAME TABLE stripe_recurring_payment TO scheduled_rent_payment');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('DROP TABLE late_rent_payment_settings');
        $this->addSql('DROP TABLE rent_payment');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rent_payment_transactions DROP FOREIGN KEY FK_A1D92E96AE2107D4');
        $this->addSql('CREATE TABLE late_rent_payment_settings (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, fee INT DEFAULT NULL, is_allow_rent_payment TINYINT(1) DEFAULT NULL, category VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_E889AF97A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rent_payment (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, balance INT NOT NULL, debt_start_at DATE DEFAULT NULL, UNIQUE INDEX UNIQ_8C04FF7DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A803AD8644E (user_source), INDEX IDX_F7129A80233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE late_rent_payment_settings ADD CONSTRAINT FK_E889AF97A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rent_payment ADD CONSTRAINT FK_8C04FF7DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE rent_payment_balance');
        $this->addSql('DROP TABLE late_rent_payment');
        $this->addSql('DROP TABLE rent_payment_transactions');
        $this->addSql('RENAME TABLE scheduled_rent_payment TO stripe_recurring_payment');
        $this->addSql('ALTER TABLE stripe_transactions DROP metadata');
        $this->addSql('ALTER TABLE users DROP is_allow_rent_payment');
    }
}
