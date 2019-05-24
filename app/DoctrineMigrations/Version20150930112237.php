<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150930112237 extends AbstractMigration
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

        $this->addSql('DROP TABLE IF EXISTS property_images');
        $this->addSql(
            'CREATE TABLE property_images (
            property_id INT NOT NULL,
            image_id INT NOT NULL,
            INDEX IDX_9E68D116549213EC (property_id),
            INDEX IDX_9E68D1163DA5256D (image_id),
            PRIMARY KEY(property_id, image_id))
            DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE property_images
            ADD CONSTRAINT FK_9E68D116549213EC
            FOREIGN KEY (property_id) REFERENCES properties (id)'
        );
        $this->addSql(
            'ALTER TABLE property_images
            ADD CONSTRAINT FK_9E68D1163DA5256D
            FOREIGN KEY (image_id) REFERENCES images (id)'
        );
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A549213EC');
        $this->addSql('DROP INDEX IDX_E01FBE6A549213EC ON images');
        $this->addSql(
            'ALTER TABLE images
            ADD created_date DATETIME NOT NULL,
            ADD updated_date DATETIME NOT NULL,
            DROP property_id'
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

        $this->addSql('DROP TABLE property_images');
        $this->addSql(
            'ALTER TABLE images
            ADD property_id INT NOT NULL,
            DROP created_date,
            DROP updated_date'
        );
        $this->addSql(
            'ALTER TABLE images
            ADD CONSTRAINT FK_E01FBE6A549213EC
            FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE'
        );
        $this->addSql('CREATE INDEX IDX_E01FBE6A549213EC ON images (property_id)');
        $this->addSql(
            'ALTER TABLE users
            ADD image LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci'
        );
    }
}
