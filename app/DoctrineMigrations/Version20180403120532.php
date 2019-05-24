<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180403120532 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE application_forms DROP FOREIGN KEY FK_1EABFC8D549213EC;');
        $this->addSql('ALTER TABLE application_forms ADD CONSTRAINT FK_1EABFC8D549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE;');
        $this->addSql('ALTER TABLE application_sections DROP FOREIGN KEY FK_94AD9C8BB4489E4E;');
        $this->addSql('DROP INDEX IDX_94AD9C8BB4489E4E ON application_sections;');
        $this->addSql('ALTER TABLE application_sections CHANGE application_form_id application_section_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE application_sections ADD CONSTRAINT FK_94AD9C8BAFA0D296 FOREIGN KEY (application_section_id) REFERENCES application_forms (id) ON DELETE CASCADE;');
        $this->addSql('CREATE INDEX IDX_94AD9C8BAFA0D296 ON application_sections (application_section_id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application_forms DROP FOREIGN KEY FK_1EABFC8D549213EC;');
        $this->addSql('ALTER TABLE application_forms ADD CONSTRAINT FK_1EABFC8D549213EC FOREIGN KEY (property_id) REFERENCES properties (id);');
        $this->addSql('ALTER TABLE application_sections DROP FOREIGN KEY FK_94AD9C8BAFA0D296;');
        $this->addSql('DROP INDEX IDX_94AD9C8BAFA0D296 ON application_sections;');
        $this->addSql('ALTER TABLE application_sections CHANGE application_section_id application_form_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE application_sections ADD CONSTRAINT FK_94AD9C8BB4489E4E FOREIGN KEY (application_form_id) REFERENCES application_forms (id);');
        $this->addSql('CREATE INDEX IDX_94AD9C8BB4489E4E ON application_sections (application_form_id);');

    }
}
