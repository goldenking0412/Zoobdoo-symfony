<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151009104543 extends AbstractMigration
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
            'CREATE TABLE appointment_requests
            (id INT AUTO_INCREMENT NOT NULL,
            property_id INT DEFAULT NULL,
            user_name VARCHAR(255) NOT NULL,
            phone VARCHAR(255) DEFAULT NULL,
            email VARCHAR(60) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            message LONGTEXT DEFAULT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            INDEX IDX_4EA15D99549213EC (property_id),
            PRIMARY KEY(id))
            DEFAULT CHARACTER SET utf8
            COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE appointment_requests ADD CONSTRAINT FK_4EA15D99549213EC FOREIGN KEY (property_id) REFERENCES properties (id)'
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

        $this->addSql('DROP TABLE appointment_requests');
    }
}
