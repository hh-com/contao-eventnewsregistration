<?php

namespace Hhcom\ContaoEventNewsRegistration\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Hhcom\ContaoEventNewsRegistration\ContaoEventNewsRegistration;

class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
	
	 /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoEventNewsRegistration::class) 
                ->setLoadAfter([
                    ContaoCoreBundle::class
                    ]) 
                ->setReplace( [ 'contao-eventnewsregistration' ])
                ,
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {


        return $resolver
            ->resolve(__DIR__.'/../Resources/config/routing.yml')
            ->load(__DIR__.'/../Resources/config/routing.yml')
        ;
    }
}