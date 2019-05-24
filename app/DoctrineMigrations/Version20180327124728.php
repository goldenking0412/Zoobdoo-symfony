<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180327124728 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE application_forms DROP FOREIGN KEY FK_1EABFC8DA76ED395;');
        $this->addSql('DROP INDEX UNIQ_1EABFC8DA76ED395 ON application_forms;');
        $this->addSql('ALTER TABLE application_forms CHANGE user_id property_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE application_forms ADD CONSTRAINT FK_1EABFC8D549213EC FOREIGN KEY (property_id) REFERENCES properties (id);');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1EABFC8D549213EC ON application_forms (property_id);');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE application_forms CHANGE property_id user_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE application_forms ADD CONSTRAINT FK_1EABFC8DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id);');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1EABFC8DA76ED395 ON application_forms (user_id);');
    }
}
