<?php

namespace InstagramScraper\Model\Response;

use InstagramScraper\Model\Account;
use InstagramScraper\Model\Story;

/**
 * Class AccountResponse
 * @package InstagramScraper\Model
 */
class StoriesResponse
{
    /**
     * Account
     * @var Account
     */
    public $account;


    public $hasPublicStory = false;

    /**
     * @var Story[]
     */
    public $stories;







    /**
     * @var PageInfo
     */
    public $pageInfo;

    public function __construct()
    {
        $this->pageInfo = new PageInfo();
    }
}
