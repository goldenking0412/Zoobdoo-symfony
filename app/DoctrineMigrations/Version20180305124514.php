<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180305124514 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE rent_payment_transactions');
        $this->addSql('ALTER TABLE rent_payment_balance ADD debt_start_at DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE late_rent_payment DROP is_paid');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rent_payment_transactions (rent_payment_id INT NOT NULL, transaction_id INT NOT NULL, UNIQUE INDEX UNIQ_A1D92E962FC0CB0F (transaction_id), INDEX IDX_A1D92E96AE2107D4 (rent_payment_id), PRIMARY KEY(rent_payment_id, transaction_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rent_payment_transactions ADD CONSTRAINT FK_A1D92E962FC0CB0F FOREIGN KEY (transaction_id) REFERENCES stripe_transactions (id)');
        $this->addSql('ALTER TABLE rent_payment_transactions ADD CONSTRAINT FK_A1D92E96AE2107D4 FOREIGN KEY (rent_payment_id) REFERENCES late_rent_payment (id)');
        $this->addSql('ALTER TABLE late_rent_payment ADD is_paid TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE rent_payment_balance DROP debt_start_at');
    }
}
