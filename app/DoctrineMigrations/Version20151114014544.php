<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151114014544 extends AbstractMigration
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
            'CREATE TABLE application_forms (
            id INT AUTO_INCREMENT NOT NULL,
            property_id INT DEFAULT NULL,
            is_default TINYINT(1) NOT NULL,
            created_date DATETIME NOT NULL,
            updated_date DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_1EABFC8D549213EC (property_id),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE application_forms ADD CONSTRAINT FK_1EABFC8D549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('ALTER TABLE application_sections DROP FOREIGN KEY FK_94AD9C8B549213EC');
        $this->addSql('DROP INDEX IDX_94AD9C8B549213EC ON application_sections');
        $this->addSql('ALTER TABLE application_sections ADD form_id INT DEFAULT NULL, DROP property_id');
        $this->addSql('ALTER TABLE application_sections ADD CONSTRAINT FK_94AD9C8B5FF69B7D FOREIGN KEY (form_id) REFERENCES application_forms (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_94AD9C8B5FF69B7D ON application_sections (form_id)');
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $command = "mysql -u{$this->connection->getUsername()} -p{$this->connection->getPassword()} "
            . "-h {$this->connection->getHost()} --port={$this->connection->getPort()} -D {$this->connection->getDatabase()} < ";

        shell_exec($command . __DIR__ . '/sources/default_application_forms.sql');
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

        $this->addSql('ALTER TABLE application_sections DROP FOREIGN KEY FK_94AD9C8B5FF69B7D');
        $this->addSql('DROP TABLE application_forms');
        $this->addSql('DROP INDEX IDX_94AD9C8B5FF69B7D ON application_sections');
        $this->addSql('ALTER TABLE application_sections ADD property_id INT NOT NULL, DROP form_id');
        $this->addSql('ALTER TABLE application_sections ADD CONSTRAINT FK_94AD9C8B549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('CREATE INDEX IDX_94AD9C8B549213EC ON application_sections (property_id)');
    }
}
