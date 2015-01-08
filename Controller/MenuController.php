<?php

namespace PBE\BaseBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

// this imports the "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class MenuController extends Controller
{
    /**
     * @Template()
     * @param int $parentFolderLocationId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function topMenuFromFolderAction( $parentFolderLocationId )
    {
        $repository = $this->getRepository();

        /** @var \eZ\Publish\Core\Repository\Values\Content\Location $parentFolderContent */
        $parentFolderLocation = $repository->getLocationService()->loadLocation( $parentFolderLocationId );

        // build the menu tree
        $menuTree = $this->buildMenuTree( $parentFolderLocation );

        return $this->render('PBEBaseBundle:Menu:topMenuFromFolder.html.twig', array( 'menuTree' => $menuTree ) );
    }

    /**
     * @param Content\Location $location
     * @return array
     */
    private function buildMenuTree( \eZ\Publish\API\Repository\Values\Content\Location $location )
    {
        $repository = $this->getRepository();
        $contentTypeService = $this->getRepository()->getContentTypeService();
        $contentService = $repository->getContentService();

        $childLocationList = $repository->getLocationService()->loadLocationChildren( $location );
        $requestUri = strtolower( $_SERVER["REQUEST_URI"] );
        $menuTree = array();

        foreach ( $childLocationList->locations as $childLocation )
        {
            $contentType = $contentTypeService->loadContentType( $childLocation->contentInfo->contentTypeId );

            switch ( $contentType->identifier )
            {
                case "folder":
                    // if a folder was found, build the tree underneath
                    $tree = $this->buildMenuTree( $childLocation );

                    // check if a sub-item is the active menu item
                    $isActive = false;
                    foreach ( $tree as $treeItem )
                    {
                        if ( $treeItem["active"] )
                        {
                            $isActive = true;
                            break;
                        }
                    }

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
                    $isActive = strpos( $requestUri, strtolower( $urlLocation->link ) ) !== false;

                    $data = array(
                        "name" => $childLocation->getContentInfo()->name,
                        "link" => $urlLocation->link,
                        "active" => $isActive
                    );
                    $menuTree[] = $data;
                    break;
            }
        }

        return $menuTree;
    }
}
