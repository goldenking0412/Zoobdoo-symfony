<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151123175148 extends AbstractMigration
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
            'CREATE TABLE homepage_content (
            id INT AUTO_INCREMENT NOT NULL,
            service_body LONGTEXT NOT NULL,
            feature_body LONGTEXT NOT NULL,
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $command = "mysql -u{$this->connection->getUsername()} -p{$this->connection->getPassword()} "
            . "-h {$this->connection->getHost()} --port={$this->connection->getPort()} -D {$this->connection->getDatabase()} < ";

        shell_exec($command . __DIR__ . '/sources/homepage_content.sql');
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

        $this->addSql('DROP TABLE homepage_content');
    }
}
