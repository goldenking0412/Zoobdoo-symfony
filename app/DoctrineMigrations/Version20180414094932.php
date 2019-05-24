<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180414094932 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE stripe_account DROP day_of_birth, DROP month_of_birth, DROP year_of_birth;');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE stripe_account ADD day_of_birth VARCHAR(255) DEFAULT NULL, ADD month_of_birth VARCHAR(255) DEFAULT NULL, ADD year_of_birth VARCHAR(255) DEFAULT NULL;');
    }
}
