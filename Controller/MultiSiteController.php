<?php

namespace PBE\BaseBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use PBE\BaseBundle\Helper\MultiSiteHelper;

// this imports the "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class MultiSiteController extends Controller
{
    /**
     * @Template()
     * @param $locationId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function canonicalLinkAction( $locationId, $locale )
    {
        $repository = $this->getRepository();
        $location = $repository->getLocationService()->loadLocation( $locationId );
        $location->contentInfo->mainLocationId;

        $helper = new MultiSiteHelper( $this->getConfigResolver(), $repository->getLocationService(), $repository->getURLAliasService() );
        $url = $helper->getCanonicalUrl( $locationId, $locale );

        return $this->render('PBEBaseBundle:MultiSite:canonicalLink.html.twig', array( 'url' => $url ) );
    }
}
