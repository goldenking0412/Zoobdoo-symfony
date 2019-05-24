<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160201162958 extends AbstractMigration
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
            'CREATE TABLE contract_forms (
            id INT AUTO_INCREMENT NOT NULL,
            property_id INT DEFAULT NULL,
            is_default TINYINT(1) NOT NULL,
            is_published TINYINT(1) NOT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_PROPERTY (property_id),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE contract_sections (
            id INT AUTO_INCREMENT NOT NULL,
            form_id INT DEFAULT NULL,
            name VARCHAR(255) NOT NULL,
            sort INT NOT NULL,
            content LONGTEXT DEFAULT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            INDEX IDX_FORM (form_id), PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE contract_forms ADD CONSTRAINT FK_PROPERTY FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('ALTER TABLE contract_sections ADD CONSTRAINT FK_FORM FOREIGN KEY (form_id) REFERENCES contract_forms (id) ON DELETE CASCADE');
        $this->addSql(
            'ALTER TABLE users
            ADD contract_form_counter INT DEFAULT 0,
            CHANGE property_counter property_counter INT DEFAULT 1,
            CHANGE application_form_counter application_form_counter INT DEFAULT 0'
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

        $this->addSql('ALTER TABLE contract_sections DROP FOREIGN KEY FK_FORM');
        $this->addSql('DROP TABLE contract_forms');
        $this->addSql('DROP TABLE contract_sections');
        $this->addSql('ALTER TABLE users DROP contract_form_counter');
    }
}
