<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151130163818 extends AbstractMigration
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
            'CREATE TABLE pro_report (
            id INT AUTO_INCREMENT NOT NULL,
            pro_consultant_id INT NOT NULL,
            count_users INT NOT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            approved_date DATETIME NOT NULL,
            INDEX IDX_622DFBD3D9376F81 (pro_consultant_id),
            PRIMARY KEY(id))
            DEFAULT CHARACTER SET utf8
            COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE pro_report
            ADD CONSTRAINT FK_622DFBD3D9376F81
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

        $this->addSql('DROP TABLE pro_report');
    }
}
