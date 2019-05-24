<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180423121109 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE scheduled_rent_payment ADD property_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE scheduled_rent_payment ADD CONSTRAINT FK_5A607FEC549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_5A607FEC549213EC ON scheduled_rent_payment (property_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE scheduled_rent_payment DROP FOREIGN KEY FK_5A607FEC549213EC');
        $this->addSql('DROP INDEX IDX_5A607FEC549213EC ON scheduled_rent_payment');
        $this->addSql('ALTER TABLE scheduled_rent_payment DROP property_id');
    }
}
