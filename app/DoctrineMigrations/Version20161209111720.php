<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161209111720 extends AbstractMigration
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

        $this->addSql('ALTER TABLE users ADD is_active_monthly_fee TINYINT(1) DEFAULT \'0\' NOT NULL');

        $stmt = $this->connection->prepare('
            SELECT u.id FROM users u
            JOIN ps_customers ps_c ON ps_c.user_id = u.id
            
        ');
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $this->addSql('UPDATE users SET is_active_monthly_fee = \'1\' WHERE id=' . $row['id']);
        }
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

        $this->addSql('ALTER TABLE users DROP is_active_monthly_fee');
    }
}
