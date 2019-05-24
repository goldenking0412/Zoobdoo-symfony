<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151103153328 extends AbstractMigration
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
            'CREATE TABLE application_fields (
            id INT AUTO_INCREMENT NOT NULL,
            section_id INT NOT NULL,
            type ENUM(\'file\',\'text\',\'checkbox\',\'radio\') NOT NULL DEFAULT \'text\',
            name VARCHAR(255) DEFAULT NULL,
            data LONGTEXT DEFAULT NULL,
            sort INT NOT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            INDEX IDX_CED6EF4DD823E37A (section_id),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE application_sections (
            id INT AUTO_INCREMENT NOT NULL,
            property_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            sort INT NOT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_94AD9C8B549213EC (property_id),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE application_fields ADD CONSTRAINT FK_CED6EF4DD823E37A FOREIGN KEY (section_id) REFERENCES application_sections (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_sections ADD CONSTRAINT FK_94AD9C8B549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
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

        $this->addSql('ALTER TABLE application_fields DROP FOREIGN KEY FK_CED6EF4DD823E37A');
        $this->addSql('DROP TABLE application_fields');
        $this->addSql('DROP TABLE application_sections');
    }
}
