<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180222084557 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE charges (id INT NOT NULL, manager_id INT DEFAULT NULL, landlord_id INT DEFAULT NULL, status TINYINT(1) NOT NULL, created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, amount VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_3AEF501A783E3463 (manager_id), INDEX IDX_3AEF501AD48E7AED (landlord_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;');
        $this->addSql('ALTER TABLE charges ADD CONSTRAINT FK_3AEF501A783E3463 FOREIGN KEY (manager_id) REFERENCES users (id);');
        $this->addSql('ALTER TABLE charges ADD CONSTRAINT FK_3AEF501AD48E7AED FOREIGN KEY (landlord_id) REFERENCES users (id);');
        $this->addSql('ALTER TABLE users CHANGE manager_id manager_id INT DEFAULT NULL;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE charges;');
    }
}
