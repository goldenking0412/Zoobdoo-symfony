<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151007161353 extends AbstractMigration
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
            'CREATE TABLE messages (
            id INT AUTO_INCREMENT NOT NULL,
            from_user_id INT DEFAULT NULL,
            to_user_id INT DEFAULT NULL,
            subject VARCHAR(255) NOT NULL,
            text LONGTEXT NOT NULL,
            created_date DATETIME NOT NULL,
            is_read TINYINT(1) NOT NULL,
            INDEX IDX_DB021E962130303A (from_user_id),
            INDEX IDX_DB021E9629F6EE60 (to_user_id),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E962130303A FOREIGN KEY (from_user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E9629F6EE60 FOREIGN KEY (to_user_id) REFERENCES users (id) ON DELETE CASCADE');
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

        $this->addSql('DROP TABLE messages');
    }
}
