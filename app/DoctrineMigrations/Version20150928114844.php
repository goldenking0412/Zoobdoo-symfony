<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150928114844 extends AbstractMigration
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
            'CREATE TABLE invited_users
            (id INT AUTO_INCREMENT NOT NULL,
            user_id INT DEFAULT NULL,
            invited_email VARCHAR(60) DEFAULT NULL,
            invited_code VARCHAR(50) DEFAULT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            INDEX IDX_19D0220EA76ED395 (user_id),
            INDEX email_index (invited_email),
            PRIMARY KEY(id))
            DEFAULT CHARACTER SET utf8
            COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE invited_users ADD CONSTRAINT FK_19D0220EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)'
        );
        $this->addSql(
            'ALTER TABLE users CHANGE status status ENUM(\'pending\',\'active\',\'not_confirmed\') DEFAULT \'not_confirmed\''
        );
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

        $this->addSql('DROP TABLE invited_users');
    }
}
