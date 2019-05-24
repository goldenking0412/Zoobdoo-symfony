<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;


class Version20180219141631 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE users ADD manager_id INTEGER');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9783E3463 FOREIGN KEY (manager_id) REFERENCES users (id);');
        $this->addSql('CREATE INDEX IDX_1483A5E9783E3463 ON users (manager_id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9783E3463;');
        $this->addSql('DROP INDEX IDX_1483A5E9783E3463 ON users;');
        $this->addSql('ALTER TABLE users DROP manager_id;');
    }
}
