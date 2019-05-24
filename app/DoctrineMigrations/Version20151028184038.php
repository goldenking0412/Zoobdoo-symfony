<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151028184038 extends AbstractMigration
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
            'CREATE TABLE ps_recurring_payment (
                id INT AUTO_INCREMENT NOT NULL,
                ps_customer_id INT DEFAULT NULL,
                recurring_id INT NOT NULL,
                monthly_amount DOUBLE PRECISION DEFAULT NULL,
                subscription_type ENUM(\'cc\',\'ba\') NOT NULL DEFAULT \'cc\',
                account_id INT NOT NULL,
                start_date DATETIME NOT NULL,
                next_date DATETIME NOT NULL,
                status ENUM(\'active\',\'pending\',\'cancelled\') NOT NULL DEFAULT \'pending\',
                created_date DATETIME NOT NULL,
                updated_date DATETIME NOT NULL,
                INDEX IDX_5EF8F038A176DAA (ps_customer_id),
                PRIMARY KEY(id))
                DEFAULT CHARACTER SET utf8
                COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE ps_recurring_payment
            ADD CONSTRAINT FK_5EF8F038A176DAA
            FOREIGN KEY (ps_customer_id) REFERENCES ps_customers (id)'
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

        $this->addSql('DROP TABLE ps_recurring_payment');
    }
}
