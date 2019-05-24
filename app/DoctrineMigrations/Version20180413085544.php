<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180413085544 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE erp_notification_notification (id INT AUTO_INCREMENT NOT NULL, user_notification_id INT DEFAULT NULL, days_before INT NOT NULL, INDEX IDX_28513D1EFDC6F10B (user_notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE erp_notification_alert (id INT AUTO_INCREMENT NOT NULL, user_notification_id INT DEFAULT NULL, days_after INT NOT NULL, INDEX IDX_93DFA3DFDC6F10B (user_notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE erp_notification_user_notification (id INT AUTO_INCREMENT NOT NULL, is_send_alert_automatically INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE erp_notification_user_notification_property (user_notification_id INT NOT NULL, property_id INT NOT NULL, INDEX IDX_91F97FAEFDC6F10B (user_notification_id), INDEX IDX_91F97FAE549213EC (property_id), PRIMARY KEY(user_notification_id, property_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE erp_notification_notification ADD CONSTRAINT FK_28513D1EFDC6F10B FOREIGN KEY (user_notification_id) REFERENCES erp_notification_user_notification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE erp_notification_alert ADD CONSTRAINT FK_93DFA3DFDC6F10B FOREIGN KEY (user_notification_id) REFERENCES erp_notification_user_notification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE erp_notification_user_notification_property ADD CONSTRAINT FK_91F97FAEFDC6F10B FOREIGN KEY (user_notification_id) REFERENCES erp_notification_user_notification (id)');
        $this->addSql('ALTER TABLE erp_notification_user_notification_property ADD CONSTRAINT FK_91F97FAE549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE erp_notification_notification DROP FOREIGN KEY FK_28513D1EFDC6F10B');
        $this->addSql('ALTER TABLE erp_notification_alert DROP FOREIGN KEY FK_93DFA3DFDC6F10B');
        $this->addSql('ALTER TABLE erp_notification_user_notification_property DROP FOREIGN KEY FK_91F97FAEFDC6F10B');
        $this->addSql('DROP TABLE erp_notification_notification');
        $this->addSql('DROP TABLE erp_notification_alert');
        $this->addSql('DROP TABLE erp_notification_user_notification');
        $this->addSql('DROP TABLE erp_notification_user_notification_property');
    }
}
