<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Erp\UserBundle\ErpUserBundle(),
            new Erp\CoreBundle\ErpCoreBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\TranslationBundle\SonataTranslationBundle(),
            new Erp\PropertyBundle\ErpPropertyBundle(),
            new Erp\AdminBundle\ErpAdminBundle(),
            new Erp\SiteBundle\ErpSiteBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
            new Erp\PaymentBundle\ErpPaymentBundle(),
            new Erp\SignatureBundle\ErpSignatureBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new EE\DataExporterBundle\EEDataExporterBundle(),
            new Slik\DompdfBundle\SlikDompdfBundle(),
            new FOS\CKEditorBundle\FOSCKEditorBundle(),
            new WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),
            new Erp\SmartMoveBundle\ErpSmartMoveBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
            new Bukashk0zzz\HelloSignBundle\Bukashk0zzzHelloSignBundle(),
            new Erp\StripeBundle\ErpStripeBundle(),
            new Erp\PlaidBundle\ErpPlaidBundle(),
            new Shivas\VersioningBundle\ShivasVersioningBundle(),
            new Erp\NotificationBundle\ErpNotificationBundle(),
            new Erp\VendorBundle\ErpVendorBundle(),
            new Erp\WorkorderBundle\ErpWorkorderBundle(),
            new Erp\OlarkBundle\ErpOlarkBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
