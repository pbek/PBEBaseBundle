<?php

namespace PBE\BaseBundle\Twig;

use eZ\Publish\API\Repository\Repository;
use Twig_SimpleFunction;


class BaseExtension extends \Twig_Extension
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    public function __construct(
        Repository $repository
    )
    {
        $this->repository = $repository;
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction(
                'pbe_fetch_content',
                array( $this, 'fetchContent' )
            ),
        );
    }

    /**
     * fetches content
     *
     * @param $contentId
     * @return \eZ\Publish\Core\Repository\Values\Content\Content|NULL
     *
     */
    public function fetchContent( $contentId )
    {
        /** @var \eZ\Publish\Core\Repository\Values\Content\Content $content */
        $content = $this->repository->getContentService()->loadContent( $contentId );

        return $content;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pbe.base';
    }
}
