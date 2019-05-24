<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151019123519 extends AbstractMigration
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

        $this->addSql('ALTER TABLE invited_users DROP FOREIGN KEY FK_19D0220EA76ED395');
        $this->addSql('DROP INDEX IDX_19D0220EA76ED395 ON invited_users');
        $this->addSql('ALTER TABLE invited_users CHANGE user_id property_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invited_users ADD CONSTRAINT FK_19D0220E549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('CREATE INDEX IDX_19D0220E549213EC ON invited_users (property_id)');
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

        $this->addSql('ALTER TABLE invited_users DROP FOREIGN KEY FK_19D0220E549213EC');
        $this->addSql('DROP INDEX IDX_19D0220E549213EC ON invited_users');
        $this->addSql('ALTER TABLE invited_users CHANGE property_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invited_users ADD CONSTRAINT FK_19D0220EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_19D0220EA76ED395 ON invited_users (user_id)');
    }
}
