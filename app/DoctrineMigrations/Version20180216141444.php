<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180216141444 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sm_renters DROP FOREIGN KEY FK_A4564241D48E7AED');
        $this->addSql('DROP INDEX IDX_A4564241D48E7AED ON sm_renters');
        $this->addSql('ALTER TABLE sm_renters CHANGE landlord_id manager_id INT NOT NULL');
        $this->addSql('ALTER TABLE sm_renters ADD CONSTRAINT FK_A4564241783E3463 FOREIGN KEY (manager_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_A4564241783E3463 ON sm_renters (manager_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sm_renters DROP FOREIGN KEY FK_A4564241783E3463');
        $this->addSql('DROP INDEX IDX_A4564241783E3463 ON sm_renters');
        $this->addSql('ALTER TABLE sm_renters CHANGE manager_id landlord_id INT NOT NULL');
        $this->addSql('ALTER TABLE sm_renters ADD CONSTRAINT FK_A4564241D48E7AED FOREIGN KEY (landlord_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_A4564241D48E7AED ON sm_renters (landlord_id)');
    }
}
