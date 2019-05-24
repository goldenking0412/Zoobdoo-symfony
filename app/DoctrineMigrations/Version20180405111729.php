<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180405111729 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE static_pages SET content=\'<div class="row">
<div class="col-md-2 col-md-offset-1"><img alt="secure.png" src="/assets/images/payonline.png" /><p>Pay rent online (ACH and Credit Card Accepted)</p>
</div>

<div class="col-md-2"><img alt="secure.png" src="/assets/images/maintenance.png" /><p>Automated maintenance requests</p>
</div>

<div class="col-md-2"><img alt="secure.png" src="/assets/images/application.png" /><p>E-sign applications and contracts</p>
</div>

<div class="col-md-2"><img alt="secure.png" src="/assets/images/secure.png" /><p>Keep information safe with multiple firewalls, SSL certification, and PCI compliance verification.</p>
</div>

<div class="col-md-2"><img alt="secure.png" src="/assets/images/cycle.png" /><p>No monthly fee.</p>
</div>
</div>

<p> </p>

<p> </p>

<h3><a href="https://zoobdoo.com/contact" style="color:rgb(85,85,85);" title="Contact Zoobdoo">Contact us</a> today to learn more about our tenant features.</h3>

<p>Only a small flat fee of $3 charged per ACH transaction.</p>

<p>2.5% charged per credit card transaction.</p>

<p> </p>\' WHERE code = \'tenant-features\';');

        $this->addSql('UPDATE static_pages SET content=\'<div class="container">
<div class="row">
<div class="col-xs-3 features"><img alt="integrate.png" src="/assets/images/integrate.png" /><p>Integrate your property management website.</p>
</div>
<div class="col-xs-3 features"><img alt="payment.png" src="/assets/images/payment.png" /><p>Process online payments straight to your account</p>
</div>
<div class="col-xs-3 features"><img alt="application.png" src="/assets/images/application.png" /><p>Accept applications electronically and receive application fees.</p>
</div>
<div class="col-xs-3 features"><img alt="thumbs.png" src="/assets/images/thumbs.png" /><p>Run credit, criminal, and eviction history.</p>
</div>
</div>
<div class="row">
<div class="col-xs-3 features"><img alt="maintenance.png" src="/assets/images/maintenance.png" /><p>Automated maintenance requests.</p>
</div>
<div class="col-xs-3 features"><img alt="2.png" src="/assets/images/2.png" /><p>E-sign rental applications and contracts.</p>
</div>
<div class="col-xs-3 features"><img alt="cycle.png" src="/assets/images/cycle.png" /><p>No contracts! Sign-up now with no obligations.</p>
</div>
<div class="col-xs-3 features"><img alt="secure.png" src="/assets/images/secure.png" /><p>Keep information safe with multiple firewalls, SSL certification, and PCI compliance verification.</p>
</div>
</div>
<div class="row">
<div class="col-xs-12">
<h3>No setup or monthly fees! No contracts or obligations!</h3>
</div>
</div>
</div>\' WHERE code = \'manager-features\';');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE static_pages SET content=\'<div class="row">
<div class="col-md-2 col-md-offset-1"><img alt="secure.png" src="/assets/images/payonline.png" /><p>Pay rent online (ACH and Credit Card Accepted)</p>
<a class="learnmore" href="/register">Learn More</a></div>

<div class="col-md-2"><img alt="secure.png" src="/assets/images/maintenance.png" /><p>Automated maintenance requests</p>
<a class="learnmore" href="/register">Learn More</a></div>

<div class="col-md-2"><img alt="secure.png" src="/assets/images/application.png" /><p>E-sign applications and contracts</p>
<a class="learnmore" href="/register">Learn More</a></div>

<div class="col-md-2"><img alt="secure.png" src="/assets/images/secure.png" /><p>Keep information safe with multiple firewalls, SSL certification, and PCI compliance verification.</p>
<a class="learnmore" href="/register">Learn More</a></div>

<div class="col-md-2"><img alt="secure.png" src="/assets/images/cycle.png" /><p>No monthly fee.</p>
<a class="learnmore" href="/register">Learn More</a></div>
</div>

<p> </p>

<p> </p>

<h3><a href="https://zoobdoo.com/contact" style="color:rgb(85,85,85);" title="Contact Zoobdoo">Contact us</a> today to learn more about our tenant features.</h3>

<p>Only a small flat fee of $3 charged per ACH transaction.</p>

<p>2.5% charged per credit card transaction.</p>

<p> </p>\' WHERE code = \'tenant-features\';');

        $this->addSql('UPDATE static_pages SET content=\'<div class="container">
<div class="row">
<div class="col-xs-3 features"><img alt="integrate.png" src="/assets/images/integrate.png" /><p>Integrate your property management website.</p>
<a class="learnmore" href="/register">Learn More</a></div>
<div class="col-xs-3 features"><img alt="payment.png" src="/assets/images/payment.png" /><p>Process online payments straight to your account</p>
<a class="learnmore" href="/register">Learn More</a></div>
<div class="col-xs-3 features"><img alt="application.png" src="/assets/images/application.png" /><p>Accept applications electronically and receive application fees.</p>
<a class="learnmore" href="/register">Learn More</a></div>
<div class="col-xs-3 features"><img alt="thumbs.png" src="/assets/images/thumbs.png" /><p>Run credit, criminal, and eviction history.</p>
<a class="learnmore" href="/register">Learn More</a></div>
</div>
<div class="row">
<div class="col-xs-3 features"><img alt="maintenance.png" src="/assets/images/maintenance.png" /><p>Automated maintenance requests.</p>
<a class="learnmore" href="/register">Learn More</a></div>
<div class="col-xs-3 features"><img alt="2.png" src="/assets/images/2.png" /><p>E-sign rental applications and contracts.</p>
<a class="learnmore" href="/register">Learn More</a></div>
<div class="col-xs-3 features"><img alt="cycle.png" src="/assets/images/cycle.png" /><p>No contracts! Sign-up now with no obligations.</p>
<a class="learnmore" href="/register">Learn More</a></div>
<div class="col-xs-3 features"><img alt="secure.png" src="/assets/images/secure.png" /><p>Keep information safe with multiple firewalls, SSL certification, and PCI compliance verification.</p>
<a class="learnmore" href="/register">Learn More</a></div>
</div>
<div class="row">
<div class="col-xs-12">
<h3>No setup or monthly fees! No contracts or obligations!</h3>
</div>
</div>
</div>\' WHERE code = \'manager-features\';');



    }
}
