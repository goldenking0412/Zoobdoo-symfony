<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151007164513 extends AbstractMigration
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

        $this->addSql('ALTER TABLE properties ADD tenant_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C7B5F891A1 FOREIGN KEY (tenant_user_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87C331C7B5F891A1 ON properties (tenant_user_id)');
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

        $this->addSql('ALTER TABLE properties DROP FOREIGN KEY FK_87C331C7B5F891A1');
        $this->addSql('DROP INDEX UNIQ_87C331C7B5F891A1 ON properties');
        $this->addSql('ALTER TABLE properties DROP tenant_user_id');
    }
}
