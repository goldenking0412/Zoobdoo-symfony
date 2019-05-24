<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180420120535 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE charges DROP FOREIGN KEY FK_3AEF501AD48E7AED;');
        $this->addSql('ALTER TABLE charges ADD CONSTRAINT FK_3AEF501AD48E7AED FOREIGN KEY (landlord_id) REFERENCES users (id) ON DELETE CASCADE;');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE charges DROP FOREIGN KEY FK_3AEF501AD48E7AED;');
        $this->addSql('ALTER TABLE charges ADD CONSTRAINT FK_3AEF501AD48E7AED FOREIGN KEY (landlord_id) REFERENCES users (id);');
    }
}
