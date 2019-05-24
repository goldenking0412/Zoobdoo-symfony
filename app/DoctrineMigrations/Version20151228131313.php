<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151228131313 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE `users` SET `status` = "active" WHERE `enabled` = 1 and `roles` LIKE "%ROLE_SUPER_ADMIN%" ;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // not supported
    }
}
