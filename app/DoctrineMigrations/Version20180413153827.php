<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180413153827 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE erp_notification_user_notification ADD template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE erp_notification_user_notification ADD CONSTRAINT FK_3733D25B5DA0FB8 FOREIGN KEY (template_id) REFERENCES erp_notification_template (id)');
        $this->addSql('CREATE INDEX IDX_3733D25B5DA0FB8 ON erp_notification_user_notification (template_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE erp_notification_user_notification DROP FOREIGN KEY FK_3733D25B5DA0FB8');
        $this->addSql('DROP INDEX IDX_3733D25B5DA0FB8 ON erp_notification_user_notification');
        $this->addSql('ALTER TABLE erp_notification_user_notification DROP template_id');
    }
}
