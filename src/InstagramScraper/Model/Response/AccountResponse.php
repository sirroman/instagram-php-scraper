<?php

namespace InstagramScraper\Model\Response;

use InstagramScraper\Model\Account;
use InstagramScraper\Model\Media;

/**
 * Class AccountResponse
 * @package InstagramScraper\Model
 */
class AccountResponse
{
    /**
     * Account
     * @var Account
     */
    public $account;

    /**
     * @var Media[]
     */
    public $medias;

    /**
     * @var PageInfo
     */
    public $pageInfo;

    /**
     * @var string
     */
    public $rhxGis;

    public function __construct()
    {
        $this->pageInfo = new PageInfo();
    }
}
