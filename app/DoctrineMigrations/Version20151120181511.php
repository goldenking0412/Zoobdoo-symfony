<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151120181511 extends AbstractMigration
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
            'CREATE TABLE ps_deferred_payments (
            id INT AUTO_INCREMENT NOT NULL,
            ps_customer_id INT DEFAULT NULL,
            account_id INT NOT NULL,
            transaction_id INT DEFAULT NULL,
            amount DOUBLE PRECISION NOT NULL,
            payment_date DATETIME NOT NULL,
            status ENUM(\'Pending\', \'Posted\', \'Settled\', \'Authorized\', \'Failed\') NOT NULL DEFAULT \'Pending\',
            maked_date DATETIME DEFAULT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            INDEX IDX_14DCBD178A176DAA (ps_customer_id),
            PRIMARY KEY(id))
            DEFAULT CHARACTER SET utf8
            COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE ps_deferred_payments
            ADD CONSTRAINT FK_14DCBD178A176DAA FOREIGN KEY (ps_customer_id) REFERENCES ps_customers (id)'
        );
        $this->addSql('ALTER TABLE ps_history ADD user_id INT DEFAULT NULL');
        $this->addSql(
            'ALTER TABLE ps_history ADD CONSTRAINT FK_EA3D5CA1A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)'
        );
        $this->addSql('CREATE INDEX IDX_EA3D5CA1A76ED395 ON ps_history (user_id)');
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

        $this->addSql('DROP TABLE ps_deferred_payments');
        $this->addSql('ALTER TABLE ps_history DROP FOREIGN KEY FK_EA3D5CA1A76ED395');
        $this->addSql('DROP INDEX IDX_EA3D5CA1A76ED395 ON ps_history');
        $this->addSql('ALTER TABLE ps_history DROP user_id');
    }
}
