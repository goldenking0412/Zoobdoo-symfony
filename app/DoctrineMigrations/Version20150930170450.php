<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150930170450 extends AbstractMigration
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
            'CREATE TABLE property_documents (
            property_id INT NOT NULL,
            document_id INT NOT NULL,
            INDEX IDX_D35F7035549213EC (property_id),
            INDEX IDX_D35F7035C33F7837 (document_id),
            PRIMARY KEY(property_id, document_id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE property_documents
            ADD CONSTRAINT FK_D35F7035549213EC FOREIGN KEY (property_id) REFERENCES properties (id)'
        );
        $this->addSql(
            'ALTER TABLE property_documents
            ADD CONSTRAINT FK_D35F7035C33F7837 FOREIGN KEY (document_id) REFERENCES documents (id)'
        );
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288549213EC');
        $this->addSql('DROP INDEX IDX_A2B07288549213EC ON documents');
        $this->addSql('ALTER TABLE documents DROP property_id');
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

        $this->addSql('DROP TABLE property_documents');
        $this->addSql('ALTER TABLE documents ADD property_id INT NOT NULL');
        $this->addSql(
            'ALTER TABLE documents
            ADD CONSTRAINT FK_A2B07288549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE'
        );
        $this->addSql('CREATE INDEX IDX_A2B07288549213EC ON documents (property_id)');
    }
}
