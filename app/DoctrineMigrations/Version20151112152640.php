<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151112152640 extends AbstractMigration
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
            'CREATE TABLE forum_comments (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT DEFAULT NULL,
            topic_id INT DEFAULT NULL,
            text LONGTEXT NOT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            INDEX IDX_786D1BCDA76ED395 (user_id),
            INDEX IDX_786D1BCD1F55203D (topic_id),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE forum_topics (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT DEFAULT NULL,
            name VARCHAR(255) NOT NULL,
            text LONGTEXT NOT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            INDEX IDX_895975E8A76ED395 (user_id),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE forum_comments ADD CONSTRAINT FK_786D1BCDA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_comments ADD CONSTRAINT FK_786D1BCD1F55203D FOREIGN KEY (topic_id) REFERENCES forum_topics (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_topics ADD CONSTRAINT FK_895975E8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
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

        $this->addSql('ALTER TABLE forum_comments DROP FOREIGN KEY FK_786D1BCD1F55203D');
        $this->addSql('DROP TABLE forum_comments');
        $this->addSql('DROP TABLE forum_topics');
    }
}
