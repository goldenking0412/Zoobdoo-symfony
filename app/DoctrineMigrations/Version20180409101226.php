<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180409101226 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE properties_settings DROP FOREIGN KEY FK_A00479B1549213EC;');
        $this->addSql('ALTER TABLE properties_settings ADD CONSTRAINT FK_A00479B1549213EC FOREIGN KEY (property_id) REFERENCES properties (id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE properties_settings DROP FOREIGN KEY FK_A00479B1549213EC;');
        $this->addSql('ALTER TABLE properties_settings ADD CONSTRAINT FK_A00479B1549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE;');
    }
}
