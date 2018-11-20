<?php

namespace InstagramScraper\Model\Response;

use InstagramScraper\Model\Location;
use InstagramScraper\Model\Media;

/**
 * Class AccountResponse
 * @package InstagramScraper\Model
 */
class LocationResponse
{
    /**
     * @var Location
     */
    public $location;

    /**
     * @var Media[]
     */
    public $topMedias;

    /**
     * @var Media[]
     */
    public $medias;

    /**
     * @var PageInfo
     */
    public $pageInfo;

    public function __construct()
    {
        $this->pageInfo = new PageInfo();
    }
}
