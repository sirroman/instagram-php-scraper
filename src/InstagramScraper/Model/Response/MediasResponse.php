<?php

namespace InstagramScraper\Model\Response;

use InstagramScraper\Model\Media;

class MediasResponse
{
    /**
     * @var PageInfo
     */
    public $pageInfo;

    /**
     * @var Media[]
     */
    public $medias;

    /**
     * @var int
     */
    public $count;

    public function __construct()
    {
        $this->pageInfo = new PageInfo();
    }
}