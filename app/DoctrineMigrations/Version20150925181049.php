<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150925181049 extends AbstractMigration
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
            'CREATE TABLE fees_options (
            id INT AUTO_INCREMENT NOT NULL,
            erentpay DOUBLE PRECISION DEFAULT \'10\' NOT NULL,
            credit_check DOUBLE PRECISION DEFAULT \'0\' NOT NULL,
            background_check DOUBLE PRECISION DEFAULT \'1\' NOT NULL,
            ask_pro_check DOUBLE PRECISION DEFAULT \'1\' NOT NULL,
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql('DROP TABLE fees');
        $this->addSql('CREATE INDEX city_name ON cities (name)');
        $this->addSql('CREATE INDEX city_state ON cities (state_code)');
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

        $this->addSql(
            'CREATE TABLE fees (
            id INT AUTO_INCREMENT NOT NULL,
            erentpay DOUBLE PRECISION DEFAULT NULL,
            credit_check DOUBLE PRECISION DEFAULT NULL,
            background_check DOUBLE PRECISION DEFAULT NULL,
            ask_pro_check DOUBLE PRECISION DEFAULT NULL,
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql('DROP TABLE fees_options');
        $this->addSql('DROP INDEX city_name ON cities');
        $this->addSql('DROP INDEX city_state ON cities');
    }
}
