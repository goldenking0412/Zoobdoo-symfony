<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150923154924 extends AbstractMigration
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

        $this->addSql('ALTER TABLE documents CHANGE path path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE images CHANGE path path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE properties ADD state_code VARCHAR(4) NOT NULL');
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

        $this->addSql('ALTER TABLE documents CHANGE path path VARCHAR(255) DEFAULT \'\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE images CHANGE path path VARCHAR(255) DEFAULT \'\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE properties DROP state_code');
    }
}
