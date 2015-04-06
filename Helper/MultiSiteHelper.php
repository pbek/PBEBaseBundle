<?php
/**
 * File containing the MultiSiteHelper class.
 */

namespace PBE\BaseBundle\Helper;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\API\Repository\LocationService;
use \eZ\Publish\API\Repository\URLAliasService;

/**
 * Helper for multi-site setuo
 */
class MultiSiteHelper
{
    /**
     * @var  \eZ\Publish\API\Repository\LocationService
     */
    protected $locationService;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     */
    protected $urlAliasService;


    public function __construct(
        ConfigResolverInterface $configResolver,
        LocationService $locationService,
        URLAliasService $urlAliasService
    )
    {
        $this->configResolver = $configResolver;
        $this->locationService = $locationService;
        $this->urlAliasService = $urlAliasService;
    }

    /**
     * get the canonical url for a multi-site setup
     *
     * @param $locationId
     * @param $locale
     * @return string
     */
    public function getCanonicalUrl( $locationId, $locale )
    {
        $location = $this->locationService->loadLocation( $locationId );
        $mainLocationId = $location->contentInfo->mainLocationId;

        // we don't need a canonical url
        if ( $mainLocationId == $locationId )
        {
            return "";
        }

        $mainLocation = $this->locationService->loadLocation( $mainLocationId );
        $path = $mainLocation->path;

        $rootLocationId = $this->configResolver->getParameter( 'content.tree_root.location_id' );
        $urlAlias = $this->urlAliasService->reverseLookup( $mainLocation );

        // main location is in current root tree
        if ( in_array( $rootLocationId, $path ) )
        {
            return $urlAlias->path;
        }
        // main location is in an other root tree
        else
        {
            $rootLocationDepth = $this->configResolver->getParameter( 'root_location_depth', 'pbe_base' );
            $otherSiteRootLocationId = $path[$rootLocationDepth-1];

            $host = $this->getHostByRootLocationIdAndLanguage( $otherSiteRootLocationId, $locale );

            // try to get alias of $location on other site access
            $pathParts = explode ( "/", $urlAlias->path );
            for ( $i = 0; $i < ( $rootLocationDepth - 1 ); $i++ )
            {
                array_shift( $pathParts );
            }
            $path = implode( "/", $pathParts );

            return "//" . $host . "/" . $path;
        }
    }

    /**
     * @param $rootLocationId
     * @param $locale
     * @return string
     */
    public function getHostByRootLocationIdAndLanguage( $rootLocationId, $locale )
    {
        $websites = $this->configResolver->getParameter( 'websites', 'pbe_base' );
        foreach ( $websites as $website )
        {
            if ( ( $website["locale"] == $locale ) && ( $website["root_location_id"] == $rootLocationId ) )
            {
                return $website["host"];
            }
        }

        return "";
    }
}
