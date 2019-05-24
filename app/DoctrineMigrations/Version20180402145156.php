<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180402145156 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application_fields DROP FOREIGN KEY FK_CED6EF4DD823E37A;');
        $this->addSql('DROP INDEX IDX_CED6EF4DD823E37A ON application_fields;');
        $this->addSql('ALTER TABLE application_fields CHANGE section_id application_section_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE application_fields ADD CONSTRAINT FK_CED6EF4DAFA0D296 FOREIGN KEY (application_section_id) REFERENCES application_sections (id);');
        $this->addSql('CREATE INDEX IDX_CED6EF4DAFA0D296 ON application_fields (application_section_id);');
        $this->addSql('ALTER TABLE application_sections DROP FOREIGN KEY FK_94AD9C8B5FF69B7D;');
        $this->addSql('DROP INDEX IDX_94AD9C8B5FF69B7D ON application_sections;');
        $this->addSql('ALTER TABLE application_sections CHANGE form_id application_form_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE application_sections ADD CONSTRAINT FK_94AD9C8BB4489E4E FOREIGN KEY (application_form_id) REFERENCES application_forms (id);');
        $this->addSql('CREATE INDEX IDX_94AD9C8BB4489E4E ON application_sections (application_form_id);');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application_fields DROP FOREIGN KEY FK_CED6EF4DAFA0D296;');
        $this->addSql('DROP INDEX IDX_CED6EF4DAFA0D296 ON application_fields;');
        $this->addSql('ALTER TABLE application_fields CHANGE application_section_id section_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE application_fields ADD CONSTRAINT FK_CED6EF4DD823E37A FOREIGN KEY (section_id) REFERENCES application_sections (id) ON DELETE CASCADE;');
        $this->addSql('CREATE INDEX IDX_CED6EF4DD823E37A ON application_fields (section_id);');
        $this->addSql('ALTER TABLE application_sections DROP FOREIGN KEY FK_94AD9C8BB4489E4E;');
        $this->addSql('DROP INDEX IDX_94AD9C8BB4489E4E ON application_sections;');
        $this->addSql('ALTER TABLE application_sections CHANGE application_form_id form_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE application_sections ADD CONSTRAINT FK_94AD9C8B5FF69B7D FOREIGN KEY (form_id) REFERENCES application_forms (id) ON DELETE CASCADE;');
        $this->addSql('CREATE INDEX IDX_94AD9C8B5FF69B7D ON application_sections (form_id);');

    }
}
