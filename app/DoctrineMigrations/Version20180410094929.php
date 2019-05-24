<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180410094929 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE users ADD stripe_account_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9E065F932 FOREIGN KEY (stripe_account_id) REFERENCES stripe_account (id) ON DELETE CASCADE;');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E065F932 ON users (stripe_account_id);');
        $this->addSql('ALTER TABLE stripe_account DROP FOREIGN KEY FK_52F1675EA76ED395;');
        $this->addSql('ALTER TABLE stripe_account ADD CONSTRAINT FK_52F1675EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;');
        $this->addSql('ALTER TABLE stripe_transactions ADD balance_history_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE stripe_transactions ADD CONSTRAINT FK_264775DC46A68270 FOREIGN KEY (balance_history_id) REFERENCES balance_history (id) ON DELETE CASCADE;');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_264775DC46A68270 ON stripe_transactions (balance_history_id);');
        $this->addSql('ALTER TABLE balance_history DROP FOREIGN KEY FK_135152F12FC0CB0F;');
        $this->addSql('ALTER TABLE balance_history ADD CONSTRAINT FK_135152F12FC0CB0F FOREIGN KEY (transaction_id) REFERENCES stripe_transactions (id) ON DELETE CASCADE;');

        $this->addSql('ALTER TABLE properties DROP FOREIGN KEY FK_87C331C7A76ED395;');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;');

        $this->addSql('ALTER TABLE property_rent_history DROP FOREIGN KEY FK_993FB62549213EC;');
        $this->addSql('ALTER TABLE property_rent_history ADD CONSTRAINT FK_993FB62549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE;');


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9E065F932;');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E065F932 ON users;');
        $this->addSql('ALTER TABLE users DROP stripe_account_id;');
        $this->addSql('ALTER TABLE stripe_account DROP FOREIGN KEY FK_52F1675EA76ED395;');
        $this->addSql('ALTER TABLE stripe_account ADD CONSTRAINT FK_52F1675EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id);');
        $this->addSql('ALTER TABLE stripe_transactions DROP FOREIGN KEY FK_264775DC46A68270;');
        $this->addSql('DROP INDEX UNIQ_264775DC46A68270 ON stripe_transactions;');
        $this->addSql('ALTER TABLE stripe_transactions DROP balance_history_id;');


        $this->addSql('ALTER TABLE balance_history DROP FOREIGN KEY FK_135152F12FC0CB0F;');
        $this->addSql('ALTER TABLE balance_history ADD CONSTRAINT FK_135152F12FC0CB0F FOREIGN KEY (transaction_id) REFERENCES stripe_transactions (id)');

        $this->addSql('ALTER TABLE properties DROP FOREIGN KEY FK_87C331C7A76ED395;');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id);');

        $this->addSql('ALTER TABLE property_rent_history DROP FOREIGN KEY FK_993FB62549213EC;');
        $this->addSql('ALTER TABLE property_rent_history ADD CONSTRAINT FK_993FB62549213EC FOREIGN KEY (property_id) REFERENCES properties (id);');

    }
}
