<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151119150030 extends AbstractMigration
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
            'CREATE TABLE static_pages (
            id INT AUTO_INCREMENT NOT NULL,
            code VARCHAR(255) NOT NULL,
            template VARCHAR(255) NOT NULL DEFAULT \'index\',
            slug VARCHAR(255) NOT NULL,
            meta_title VARCHAR(255) DEFAULT NULL,
            meta_description LONGTEXT DEFAULT NULL,
            header_title VARCHAR(255) NOT NULL,
            title VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            in_submenu TINYINT(1) NOT NULL,
            with_submenu TINYINT(1) NOT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
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

        shell_exec($command . __DIR__ . '/sources/static_pages.sql');
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

        $this->addSql('DROP TABLE static_pages');
    }
}
