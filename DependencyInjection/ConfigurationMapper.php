<?php
/**
 * File containing the ConfigurationMap class.
 */

namespace PBE\BekerleWebBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\HookableConfigurationMapperInterface;

class ConfigurationMapper implements HookableConfigurationMapperInterface
{
    public function mapConfig( array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer )
    {
//        if ( isset( $scopeSettings['websites'] ) )
//        {
//            $scopeSettings['websites'] = array( 'websites' => $scopeSettings['websites'] );
//        }
    }

    public function preMap( array $config, ContextualizerInterface $contextualizer )
    {
        // Nothing to do here.
    }

    public function postMap( array $config, ContextualizerInterface $contextualizer )
    {
    }
}
