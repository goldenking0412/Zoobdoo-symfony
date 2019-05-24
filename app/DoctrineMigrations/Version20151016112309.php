<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151016112309 extends AbstractMigration
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
            'CREATE TABLE ps_customers (
              id INT AUTO_INCREMENT NOT NULL,
              user_id INT DEFAULT NULL,
              customer_id INT NOT NULL,
              cc_id INT DEFAULT NULL,
              ba_id INT DEFAULT NULL,
              created_date DATETIME NOT NULL,
              updated_date DATETIME NOT NULL,
              INDEX IDX_F41AF423A76ED395 (user_id),
              INDEX customer_idx (customer_id),
              PRIMARY KEY(id))
              DEFAULT CHARACTER SET utf8
              COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE ps_customers ADD CONSTRAINT FK_F41AF423A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)'
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

        $this->addSql('DROP TABLE ps_customers');
    }
}
