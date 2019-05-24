<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151120172058 extends AbstractMigration
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

        $this->addSql('ALTER TABLE property_repost_requests DROP INDEX UNIQ_BDD0902E549213EC, ADD INDEX IDX_BDD0902E549213EC (property_id)');
        $this->addSql('ALTER TABLE property_repost_requests DROP FOREIGN KEY FK_BDD0902E549213EC');
        $this->addSql('ALTER TABLE property_repost_requests ADD CONSTRAINT FK_BDD0902E549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE');
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

        $this->addSql('ALTER TABLE property_repost_requests DROP INDEX IDX_BDD0902E549213EC, ADD UNIQUE INDEX UNIQ_BDD0902E549213EC (property_id)');
        $this->addSql('ALTER TABLE property_repost_requests DROP FOREIGN KEY FK_BDD0902E549213EC');
        $this->addSql('ALTER TABLE property_repost_requests ADD CONSTRAINT FK_BDD0902E549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
    }
}
