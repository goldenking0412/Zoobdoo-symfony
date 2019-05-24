<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151120162408 extends AbstractMigration
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
            'CREATE TABLE property_repost_requests (
            id INT AUTO_INCREMENT NOT NULL,
            property_id INT DEFAULT NULL,
            status ENUM(\'new\',\'accepted\', \'rejected\') NOT NULL DEFAULT \'new\',
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_BDD0902E549213EC (property_id),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE property_repost_requests ADD CONSTRAINT FK_BDD0902E549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
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

        $this->addSql('DROP TABLE property_repost_requests');
    }
}
