<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151104145515 extends AbstractMigration
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
            'CREATE TABLE ps_history (
              id INT AUTO_INCREMENT NOT NULL,
              property_id INT NOT NULL,
              paymentDate DATETIME NOT NULL,
              amount DOUBLE PRECISION NOT NULL,
              status ENUM(\'success\',\'error\',\'pending\') NOT NULL DEFAULT \'success\',
              notes LONGTEXT DEFAULT NULL,
              created_date DATETIME NOT NULL,
              updated_date DATETIME NOT NULL,
              INDEX IDX_EA3D5CA1549213EC (property_id),
              PRIMARY KEY(id))
              DEFAULT CHARACTER SET utf8
              COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE ps_history ADD CONSTRAINT FK_EA3D5CA1549213EC FOREIGN KEY (property_id) REFERENCES properties (id)'
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

        $this->addSql('DROP TABLE ps_history');
    }
}
