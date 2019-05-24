<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160112154936 extends AbstractMigration
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

        $this->addSql(
            'ALTER TABLE sm_renters
            ADD info LONGTEXT DEFAULT NULL,
            ADD exams LONGTEXT DEFAULT NULL,
            ADD reports LONGTEXT DEFAULT NULL,
            ADD personal_token LONGTEXT NOT NULL,
            ADD exam_token LONGTEXT DEFAULT NULL,
            ADD is_personal_completed TINYINT(1) NOT NULL,
            ADD is_exam_completed TINYINT(1) NOT NULL,
            ADD is_added_as_applicant TINYINT(1) NOT NULL,
            ADD is_accepted TINYINT(1) NOT NULL,
            ADD is_payed TINYINT(1) NOT NULL,
            CHANGE sm_property_id sm_property_id INT DEFAULT NULL'
        );
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

        $this->addSql(
            'ALTER TABLE sm_renters
            DROP exams, DROP reports,
            DROP personal_token,
            DROP exam_token,
            DROP is_personal_completed,
            DROP is_exam_completed,
            DROP info,
            DROP is_accepted,
            DROP is_payed,
            DROP is_added_as_applicant'
        );
    }
}
