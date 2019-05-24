<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180428131025 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE property_images DROP FOREIGN KEY FK_9E68D1163DA5256D');
        $this->addSql('ALTER TABLE property_images DROP FOREIGN KEY FK_9E68D116549213EC');
        $this->addSql('ALTER TABLE property_images ADD CONSTRAINT FK_9E68D1163DA5256D FOREIGN KEY (image_id) REFERENCES images (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE property_images ADD CONSTRAINT FK_9E68D116549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE property_images DROP FOREIGN KEY FK_9E68D116549213EC');
        $this->addSql('ALTER TABLE property_images DROP FOREIGN KEY FK_9E68D1163DA5256D');
        $this->addSql('ALTER TABLE property_images ADD CONSTRAINT FK_9E68D116549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('ALTER TABLE property_images ADD CONSTRAINT FK_9E68D1163DA5256D FOREIGN KEY (image_id) REFERENCES images (id)');
    }
}
