<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180406130340 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE invited_users DROP FOREIGN KEY FK_19D0220E549213EC;');
        $this->addSql('ALTER TABLE invited_users ADD CONSTRAINT FK_19D0220E549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE;');
        $this->addSql('ALTER TABLE properties DROP FOREIGN KEY FK_87C331C759949888;');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C759949888 FOREIGN KEY (settings_id) REFERENCES properties_settings (id) ON DELETE CASCADE;');
        $this->addSql('ALTER TABLE properties_settings ADD property_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE properties_settings ADD CONSTRAINT FK_A00479B1549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE;');
        $this->addSql('CREATE INDEX IDX_A00479B1549213EC ON properties_settings (property_id);');
        $this->addSql('ALTER TABLE properties_settings DROP INDEX IDX_A00479B1549213EC, ADD UNIQUE INDEX UNIQ_A00479B1549213EC (property_id);');
        $this->addSql('ALTER TABLE property_rent_history DROP FOREIGN KEY FK_993FB62549213EC;');
        $this->addSql('ALTER TABLE property_rent_history ADD CONSTRAINT FK_993FB62549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE;');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE invited_users DROP FOREIGN KEY FK_19D0220E549213EC;');
        $this->addSql('ALTER TABLE invited_users ADD CONSTRAINT FK_19D0220E549213EC FOREIGN KEY (property_id) REFERENCES properties (id);');
        $this->addSql('ALTER TABLE properties DROP FOREIGN KEY FK_87C331C759949888;');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C759949888 FOREIGN KEY (settings_id) REFERENCES properties_settings (id);');
        $this->addSql('ALTER TABLE property_rent_history DROP FOREIGN KEY FK_993FB62549213EC;');
        $this->addSql('ALTER TABLE property_rent_history ADD CONSTRAINT FK_993FB62549213EC FOREIGN KEY (property_id) REFERENCES properties (id);');
        $this->addSql('ALTER TABLE properties_settings DROP FOREIGN KEY FK_A00479B1549213EC;');
        $this->addSql('DROP INDEX UNIQ_A00479B1549213EC ON properties_settings;');
        $this->addSql('ALTER TABLE properties_settings DROP property_id;');
    }
}
