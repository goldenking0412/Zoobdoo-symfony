<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151125180131 extends AbstractMigration
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
            'CREATE TABLE pro_requests (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            pro_consultant_id INT DEFAULT NULL,
            subject VARCHAR(255) NOT NULL,
            message LONGTEXT NOT NULL,
            is_refferal TINYINT(1) NOT NULL,
            consultant_fee DOUBLE PRECISION DEFAULT NULL,
            servicing_fee DOUBLE PRECISION NOT NULL,
            status ENUM(\'in_process\', \'approved\', \'payment_ok\', \'payment_error\', \'canceled\') NOT NULL DEFAULT \'in_process\',
            approved_date DATETIME DEFAULT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            INDEX IDX_6A7174CBA76ED395 (user_id),
            INDEX IDX_6A7174CBD9376F81 (pro_consultant_id),
            PRIMARY KEY(id))
            DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE pro_consultants (
            id INT AUTO_INCREMENT NOT NULL,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(255) NOT NULL,
            address LONGTEXT DEFAULT NULL,
            createdDate DATETIME NOT NULL,
            updatedDate DATETIME NOT NULL,
            PRIMARY KEY(id))
            DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE pro_requests
            ADD CONSTRAINT FK_6A7174CBA76ED395
            FOREIGN KEY (user_id)
            REFERENCES users (id)'
        );
        $this->addSql(
            'ALTER TABLE pro_requests
            ADD CONSTRAINT FK_6A7174CBD9376F81
            FOREIGN KEY (pro_consultant_id)
            REFERENCES pro_consultants (id)'
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

        $this->addSql('ALTER TABLE pro_requests DROP FOREIGN KEY FK_6A7174CBD9376F81');
        $this->addSql('DROP TABLE pro_requests');
        $this->addSql('DROP TABLE pro_consultants');
    }
}
