<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160203120147 extends AbstractMigration
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

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0;');
        $this->addSql('TRUNCATE TABLE contract_forms');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $command = "mysql -u{$this->connection->getUsername()} -p{$this->connection->getPassword()} "
            . "-h {$this->connection->getHost()} --port={$this->connection->getPort()} -D {$this->connection->getDatabase()} < ";

        shell_exec($command . __DIR__ . '/sources/default_contract_forms.sql');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // not supported
    }
}
