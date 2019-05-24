<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171211150749 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE properties CHANGE city_id city_id INT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE zip zip VARCHAR(6) DEFAULT NULL, CHANGE price price DOUBLE PRECISION DEFAULT NULL, CHANGE square_footage square_footage DOUBLE PRECISION DEFAULT NULL, CHANGE status status ENUM(\'available\',\'rented\', \'draft\', \'deleted\') DEFAULT \'draft\', CHANGE state_code state_code VARCHAR(4) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE properties CHANGE user_id user_id INT NOT NULL, CHANGE city_id city_id INT NOT NULL, CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE address address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE zip zip VARCHAR(6) NOT NULL COLLATE utf8_unicode_ci, CHANGE price price DOUBLE PRECISION NOT NULL, CHANGE square_footage square_footage DOUBLE PRECISION NOT NULL, CHANGE status status VARCHAR(255) DEFAULT \'draft\' NOT NULL COLLATE utf8_unicode_ci');
    }
}
