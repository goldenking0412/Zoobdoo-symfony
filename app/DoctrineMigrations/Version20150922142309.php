<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150922142309 extends AbstractMigration
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
            'CREATE TABLE users (
            id INT AUTO_INCREMENT NOT NULL,
            username VARCHAR(255) NOT NULL,
            username_canonical VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            email_canonical VARCHAR(255) NOT NULL,
            enabled TINYINT(1) NOT NULL,
            salt VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            last_login DATETIME DEFAULT NULL,
            locked TINYINT(1) NOT NULL,
            expired TINYINT(1) NOT NULL,
            expires_at DATETIME DEFAULT NULL,
            confirmation_token VARCHAR(255) DEFAULT NULL,
            password_requested_at DATETIME DEFAULT NULL,
            roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\',
            credentials_expired TINYINT(1) NOT NULL,
            credentials_expire_at DATETIME DEFAULT NULL,
            first_name VARCHAR(255) DEFAULT NULL,
            last_name VARCHAR(255) DEFAULT NULL,
            company_name VARCHAR(255) DEFAULT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            website_url VARCHAR(255) DEFAULT NULL,
            address_one VARCHAR(255) DEFAULT NULL,
            address_two VARCHAR(255) DEFAULT NULL,
            city VARCHAR(35) DEFAULT NULL,
            state VARCHAR(35) DEFAULT NULL,
            postal_code VARCHAR(35) DEFAULT NULL,
            is_rejected TINYINT(1) NOT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_1483A5E992FC23A8 (username_canonical),
            UNIQUE INDEX UNIQ_1483A5E9A0D96FBF (email_canonical),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql(
            'CREATE TABLE documents (
            id INT AUTO_INCREMENT NOT NULL,
            property_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            INDEX IDX_A2B07288549213EC (property_id),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql(
            'CREATE TABLE images (
            id INT AUTO_INCREMENT NOT NULL,
            property_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            INDEX IDX_E01FBE6A549213EC (property_id),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql(
            'CREATE TABLE properties (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            city_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            address VARCHAR(255) NOT NULL,
            zip VARCHAR(6) NOT NULL,
            price DOUBLE PRECISION NOT NULL,
            of_beds LONGTEXT DEFAULT NULL,
            of_baths VARCHAR(255) DEFAULT NULL,
            square_footage DOUBLE PRECISION NOT NULL,
            amenities LONGTEXT DEFAULT NULL,
            about_properties LONGTEXT DEFAULT NULL,
            additional_details LONGTEXT DEFAULT NULL,
            status ENUM(\'available\',\'rented\') NOT NULL DEFAULT \'available\',
            created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL,
            INDEX IDX_87C331C7A76ED395 (user_id),
            INDEX IDX_87C331C78BAC62AF (city_id),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql(
            'ALTER TABLE documents
            ADD CONSTRAINT FK_A2B07288549213EC
            FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE images
            ADD CONSTRAINT FK_E01FBE6A549213EC
            FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE properties
            ADD CONSTRAINT FK_87C331C7A76ED395
            FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE properties
            ADD CONSTRAINT FK_87C331C78BAC62AF
            FOREIGN KEY (city_id) REFERENCES cities (id) ON DELETE CASCADE'
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

        $this->addSql('ALTER TABLE properties DROP FOREIGN KEY FK_87C331C7A76ED395');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288549213EC');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A549213EC');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE properties');
    }
}
