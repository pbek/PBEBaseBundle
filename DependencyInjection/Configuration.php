<?php

namespace PBE\BaseBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration extends SiteAccessConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pbe_base');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $systemNode = $this->generateScopeBaseNode( $rootNode );
        $this->addMultiSiteSettings( $systemNode );

        return $treeBuilder;
    }

    private function addMultiSiteSettings( NodeBuilder $nodeBuilder )
    {
        $nodeBuilder
            ->arrayNode( 'websites' )
                ->info( 'Your websites' )
                ->example(
                    array(
                        'public_articles' => array(
                            'locale' => 'en_GB',
                            'root_location_id' => 142,
                            'host' => 'www.bekerle.com',
                        )
                    )
                )
                ->prototype( "array" )
                    ->normalizeKeys( false )
                    ->children()
                        ->scalarNode( "locale" )->info( "Locale of site" )->isRequired()->end()
                        ->integerNode( "root_location_id" )->info( "The location id of the root tree" )->isRequired()->end()
                        ->scalarNode( "host" )->info( "Hostname of site" )->isRequired()->end()
                    ->end()
                ->end()
            ->end();
    }
}
