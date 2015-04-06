<?php

namespace PBE\BaseBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

// this imports the "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

// this imports the "@Route" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MenuController extends Controller
{
    /**
     * @Template()
     * @Route("/menu/top_menu_from_folder/{parentFolderLocationId}")
     * @param int $parentFolderLocationId
     * @param array|null $directlyIncludedLocations
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function topMenuFromFolderAction( $parentFolderLocationId, $directlyIncludedLocations = null )
    {
        $repository = $this->getRepository();

        /** @var \eZ\Publish\Core\Repository\Values\Content\Location $parentFolderContent */
        $parentFolderLocation = $repository->getLocationService()->loadLocation( $parentFolderLocationId );
        
        // build the menu tree
        $menuTree = $this->buildMenuTree( $parentFolderLocation, $directlyIncludedLocations );

        return $this->render('PBEBaseBundle:Menu:topMenuFromFolder.html.twig', array( 'menuTree' => $menuTree ) );
    }

    /**
     * @param Content\Location $location
     * @param array|null $directlyIncludedLocations
     * @return array
     */
    private function buildMenuTree( \eZ\Publish\API\Repository\Values\Content\Location $location, $directlyIncludedLocations = null )
    {
        $repository = $this->getRepository();
        $contentTypeService = $this->getRepository()->getContentTypeService();
        $contentService = $repository->getContentService();

        $childLocationList = $repository->getLocationService()->loadLocationChildren( $location );
        $requestUri = strtolower( @$_SERVER["REQUEST_URI"] );
        $menuTree = array();

        foreach ( $childLocationList->locations as $childLocation )
        {
            $contentType = $contentTypeService->loadContentType( $childLocation->contentInfo->contentTypeId );
            $gotItems = false;

            // check if we should include the items of a location directly
            if ( is_array( $directlyIncludedLocations ) &&
               ( array_key_exists( $childLocation->id, $directlyIncludedLocations ) ) )
            {
                $locationData = $directlyIncludedLocations[$childLocation->id];
                $locationId = (int) $locationData["locationId"];
                $limit = (int) $locationData["limit"];

                if ( $locationId > 0 )
                {
                    // fetch directly included location
                    /** @var \eZ\Publish\Core\Repository\Values\Content\Location $directIncludeLocation */
                    $directIncludeLocation = $repository->getLocationService()->loadLocation( $locationId );

                    // build the menu tree for directly included location
                    $tree = $this->buildDirectlyIncludedMenuTree( $directIncludeLocation, $limit );

                    // check if a sub-item is the active menu item
                    $isActive = $this->hasActiveMenuEntry( $tree );

                    $data = array(
                        "name" => $childLocation->getContentInfo()->name,
                        "children" => $tree,
                        "active" => $isActive
                    );

                    // if we have a link also add the "link" to it
                    if ( $contentType->identifier == "link" )
                    {
                        // if a link was found, get the link destination
                        $content = $contentService->loadContent( $childLocation->contentId );

                        /** @var eZ\Publish\Core\FieldType\Url\Value $urlLocation */
                        $urlLocation = $content->getFieldValue( "location" );

                        // check if current menu item is the active one
                        $isLinkActive = strpos( $requestUri, strtolower( $urlLocation->link ) ) === 0;

                        $data["link"] = $urlLocation->link;
                        $data["active"] = $data["active"] || $isLinkActive;
                    }

                    $menuTree[] = $data;
                    $gotItems = true;
                }
            }

            // do the regular menu tree building if we haven't already got items
            if ( !$gotItems )
            {

                switch ( $contentType->identifier )
                {
                    case "folder":
                        // if a folder was found, build the tree underneath
                        $tree = $this->buildMenuTree( $childLocation );

                        // check if a sub-item is the active menu item
                        $isActive = $this->hasActiveMenuEntry( $tree );

                        $data = array(
                            "name" => $childLocation->getContentInfo()->name,
                            "children" => $this->buildMenuTree( $childLocation ),
                            "active" => $isActive
                        );
                        $menuTree[] = $data;
                        break;
                    case "link":
                        // if a link was found, get the link destination
                        $content = $contentService->loadContent( $childLocation->contentId );

                        /** @var eZ\Publish\Core\FieldType\Url\Value $urlLocation */
                        $urlLocation = $content->getFieldValue( "location" );

                        // check if current menu item is the active one
                        // special handling for "home" links
                        if ( $urlLocation->link == "/" )
                        {
                            $isActive = $requestUri == $urlLocation->link;
                        }
                        else
                        {
                            $isActive = strpos( $requestUri, strtolower( $urlLocation->link ) ) === 0;
                        }

                        $data = array(
                            "name" => $childLocation->getContentInfo()->name,
                            "link" => $urlLocation->link,
                            "active" => $isActive
                        );
                        $menuTree[] = $data;
                        break;
                }
            }
        }

        return $menuTree;
    }

    /**
     * build the menu tree for directly included location
     *
     * @param Content\Location $location
     * @param $limit
     * @return array
     */
    private function buildDirectlyIncludedMenuTree( \eZ\Publish\API\Repository\Values\Content\Location $location, $limit = 10 )
    {
        $repository = $this->getRepository();
        $requestUri = strtolower( @$_SERVER["REQUEST_URI"] );
        $menuTree = array();

        // fetch items of directly included location
        $childLocationList = $repository->getLocationService()->loadLocationChildren( $location, 0, $limit );
        foreach ( $childLocationList->locations as $childLocation )
        {
            // get the url alias for the location
            $urlAlias = $this->getRepository()->getURLAliasService()->reverseLookup( $childLocation );
            $link = $urlAlias->path;

            // check if current menu item is the active one
            $isActive = strpos( $requestUri, strtolower( $link ) ) === 0;

            $data = array(
                "name" => $childLocation->getContentInfo()->name,
                "link" => $link,
                "active" => $isActive
            );

            $menuTree[] = $data;
        }

        return $menuTree;
    }

    /**
     * check if a sub-item is the active menu item
     *
     * @param array $tree
     * @return bool
     */
    private function hasActiveMenuEntry( $tree )
    {
        // check if a sub-item is the active menu item
        foreach ( $tree as $treeItem )
        {
            if ( $treeItem["active"] )
            {
                return true;
            }
        }

        return false;
    }
}
