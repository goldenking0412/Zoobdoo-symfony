<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180215094820 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE users SET roles = REPLACE(roles, \'a:1:{i:0;s:13:"ROLE_LANDLORD";}\', \'a:1:{i:0;s:12:"ROLE_MANAGER";}\')');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE users SET roles = REPLACE(roles, \'a:1:{i:0;s:12:"ROLE_MANAGER";}\', \'a:1:{i:0;s:13:"ROLE_LANDLORD";}\')');

    }
}
