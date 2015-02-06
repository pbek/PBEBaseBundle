<?php

namespace PBE\BaseBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

class BaseController extends Controller
{
    /**
     * Builds a list from $searchResult.
     * Returned array consists of a hash of objects, indexed by their ID.
     *
     * @param SearchResult $searchResult
     *
     * @return array
     */
    public function buildListFromSearchResult( SearchResult $searchResult )
    {
        $list = array();
        foreach ( $searchResult->searchHits as $searchHit )
        {
            $list[$searchHit->valueObject->id] = $searchHit->valueObject;
        }

        return $list;
    }
}
