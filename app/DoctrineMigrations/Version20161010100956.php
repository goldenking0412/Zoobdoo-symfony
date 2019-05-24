<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161010100956 extends AbstractMigration
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

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0;');
        $this->addSql('TRUNCATE TABLE application_forms');
        $this->addSql('TRUNCATE TABLE application_sections');
        $this->addSql('TRUNCATE TABLE application_fields');
        $this->addSql('ALTER TABLE application_forms DROP FOREIGN KEY FK_1EABFC8D549213EC');
        $this->addSql('DROP INDEX UNIQ_1EABFC8D549213EC ON application_forms');
        $this->addSql('ALTER TABLE application_forms CHANGE property_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE application_forms ADD CONSTRAINT FK_1EABFC8DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1EABFC8DA76ED395 ON application_forms (user_id)');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1;');
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

        $this->addSql('ALTER TABLE application_forms DROP FOREIGN KEY FK_1EABFC8DA76ED395');
        $this->addSql('DROP INDEX UNIQ_1EABFC8DA76ED395 ON application_forms');
        $this->addSql('ALTER TABLE application_forms CHANGE user_id property_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE application_forms ADD CONSTRAINT FK_1EABFC8D549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1EABFC8D549213EC ON application_forms (property_id)');
    }
}
