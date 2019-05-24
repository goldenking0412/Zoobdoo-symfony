<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;


class Version20180411093654 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE static_pages SET content=\'<p>Address: <br />
3009 Sunrise Bay Ave<br />
Las Vegas, NV 89031</p>

<p>Email: info@zoobdoo.com<br />
Facebook: <a href="https://www.facebook.com/zoobdoomanager/" target="_blank" style="color:#706e7b;">https://www.facebook.com/zoobdoomanager/</a><br />
Twitter: <a href="https://twitter.com/Zoobdoo_" target="_blank" style="color:#706e7b;">https://twitter.com/Zoobdoo_</a><br />
LinkedIn: <a href="https://www.linkedin.com/company/zoobdoo-com" target="_blank" style="color:#706e7b;">https://www.linkedin.com/company/zoobdoo-com</a><br />
Pinterest: <a href="https://www.pinterest.com/zoobdoocom/pins/" target="_blank" style="color:#706e7b;">https://www.pinterest.com/zoobdoocom/pins/</a><br />
</p>\' WHERE code = \'contact\';');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE static_pages SET content=\'<p>Address: <br />
3009 Sunrise Bay Ave<br />
Las Vegas, NV 89031</p>

<p>Email: info@zoobdoo.com<br />
Facebook: <a href="https://www.facebook.com/zoobdoomanager/" style="color:#706e7b;">https://www.facebook.com/zoobdoomanager/</a><br />
Twitter: <a href="https://twitter.com/Zoobdoo_" style="color:#706e7b;">https://twitter.com/Zoobdoo_</a><br />
LinkedIn: <a href="https://www.linkedin.com/company/zoobdoo-com" style="color:#706e7b;">https://www.linkedin.com/company/zoobdoo-com</a><br />
Pinterest: <a href="https://www.pinterest.com/zoobdoocom/pins/" style="color:#706e7b;">https://www.pinterest.com/zoobdoocom/pins/</a><br />
</p>\' WHERE code = \'contact\';');



    }
}
