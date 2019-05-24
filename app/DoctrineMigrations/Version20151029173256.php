<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151029173256 extends AbstractMigration
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
            'CREATE TABLE user_documents (
                id INT AUTO_INCREMENT NOT NULL,
                from_user_id INT DEFAULT NULL,
                to_user_id INT DEFAULT NULL,
                document_id INT DEFAULT NULL,
                status ENUM(\'accepted\',\'sent\',\'recieved\') DEFAULT \'sent\',
                created_date DATETIME NOT NULL,
                updated_date DATETIME NOT NULL,
                INDEX IDX_A250FF6C2130303A (from_user_id),
                INDEX IDX_A250FF6C29F6EE60 (to_user_id),
                UNIQUE INDEX UNIQ_A250FF6CC33F7837 (document_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE user_documents ADD CONSTRAINT FK_A250FF6C2130303A FOREIGN KEY (from_user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_documents ADD CONSTRAINT FK_A250FF6C29F6EE60 FOREIGN KEY (to_user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_documents ADD CONSTRAINT FK_A250FF6CC33F7837 FOREIGN KEY (document_id) REFERENCES documents (id)');
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

        $this->addSql('DROP TABLE user_documents');
    }
}
