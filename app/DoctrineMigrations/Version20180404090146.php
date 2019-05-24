<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180404090146 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE static_pages SET content=\'<p>Address: <br />3009 Sunrise Bay Ave<br />Las Vegas, NV 89031</p><p>Email: info@zoobdoo.com<br />
Facebook: <a href="https://www.facebook.com/zoobdoomanager/" style="color:#706e7b;">https://www.facebook.com/zoobdoomanager/</a><br />
Twitter: <a href="https://twitter.com/Zoobdoo_" style="color:#706e7b;">https://twitter.com/Zoobdoo_</a><br />
LinkedIn: <a href="https://www.linkedin.com/company/zoobdoo-com" style="color:#706e7b;">https://www.linkedin.com/company/zoobdoo-com</a><br />
Pinterest: <a href="https://www.pinterest.com/zoobdoocom/pins/" style="color:#706e7b;">https://www.pinterest.com/zoobdoocom/pins/</a><br />
</p>\' WHERE code = \'contact\';');
        $this->addSql('UPDATE static_pages SET content=\'<ul class="features-list">
	<li><a href="/">Home</span></a></li>
	<li><a href="/about">About us</a></li>
	<li><a href="/manager-features">Manager features</a></li>
	<li><a href="/tenant-features">Tenant Features</a></li>
	<li><a href="/faq">FAQ</a></li>
	<li><a href="/contact">Contact</a></li>
	<li><a href="/terms-of-service">Terms of Service </a></li>
	<li><a href="/privacy-policy">Privacy Policy </a></li>
	<li><a href="/property/available">Available properties</a></li>
 <li><a href="/how-it-works">How it Works</a></li></ul>\' WHERE code = \'sitemap\';');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE static_pages SET content=\'<p>Address: <br />3009 Sunrise Bay Ave<br />Las Vegas, NV 89031</p><p>Email: info@zoobdoo.com<br />
Facebook: <a href="https://www.facebook.com/zoobdoo" style="color:#706e7b;">https://www.facebook.com/zoobdoo</a><br />
Twitter: <a href="https://twitter.com/zoobdoo" style="color:#706e7b;">https://twitter.com/zoobdoo</a><br />
LinkedIn: <a href="https://www.linkedin.com/company/zoobdoo" style="color:#706e7b;">https://www.linkedin.com/company/zoobdoo</a><br />
Pinterest: <a href="https://www.pinterest.com/zoobdoo/" style="color:#706e7b;">https://www.pinterest.com/zoobdoo/</a><br />
Google+: <a href="https://plus.google.com/u/0/b/114272088414148953802/114272088414148953802/posts/p/pub" style="color:#706e7b;">zoobdoo\\\'s Google +</a ></p>\' WHERE code = \'contact\';');
        $this->addSql('UPDATE static_pages SET content=\'<ul class="features-list"><li><a href="/">Home</span></a></li>
	<li><a href="/about">About us</a></li>
	<li><a href="/landlord-features">Landlord features</a></li>
	<li><a href="/tenant-features">Tenant Features</a></li>
	<li><a href="/faq">FAQ</a></li>
	<li><a href="/contact">Contact</a></li>
	<li><a href="/terms-of-service">Terms of Service </a></li>
	<li><a href="/privacy-policy">Privacy Policy </a></li>
	<li><a href="/property/available">Available properties</a></li>
 <li><a href="/how-it-works">How it Works</a></li></ul>\' WHERE code = \'sitemap\';');
    }
}
