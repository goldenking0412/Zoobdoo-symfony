<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150928113007 extends AbstractMigration
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
            'ALTER TABLE fees_options
            ADD created_date DATETIME NOT NULL,
            ADD updated_date DATETIME NOT NULL'
        );
        $this->addSql('INSERT INTO fees_options (id, created_date, updated_date) VALUES (1, NOW(), NOW())');
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
        $this->addSql('INSERT INTO fees_options (id, created_date, updated_date) VALUES (1, NOW(), NOW())');
        $this->addSql('ALTER TABLE fees_options DROP created_date, DROP updated_date');
    }
}
