<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180228130724 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE charges CHANGE created_date created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE charges CHANGE updated_date updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE charges CHANGE status status VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE charges CHANGE created_at created_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE charges CHANGE updated_at updated_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE charges CHANGE status status TINYINT(1) NOT NULL');
    }
}
