<?php

require '../vendor/autoload.php';

use InstagramScraper\Instagram;
use InstagramScraper\Model\Media;
use phpFastCache\CacheManager;
use PHPUnit\Framework\TestCase;


/**
 * Class InstagramTest
 * @group auth
 */
class InstagramTest extends TestCase
{
    private static $instagram;

    private static $username;
    private static $password;

    public static function setUpBeforeClass()
    {
        require 'instagramAuth.php'; // content of this file: <?php $login = "my_instagram_login"; $pass = "my_instagram_pass";
        self::$username = $login;
        self::$password = $pass;
    }


    /**
     * @throws \InstagramScraper\Exception\InstagramAuthException
     * @throws \InstagramScraper\Exception\InstagramException
     * @throws \phpFastCache\Exceptions\phpFastCacheDriverCheckException
     * @return Instagram
     */
    public static function setUpInstagram()
    {


        $sessionFolder = __DIR__ . DIRECTORY_SEPARATOR . 'sessions' . DIRECTORY_SEPARATOR;
        CacheManager::setDefaultConfig([
            'path' => $sessionFolder
        ]);

        $instanceCache = CacheManager::getInstance('files');
        self::$instagram = Instagram::withCredentials(self::$username, self::$password, $instanceCache);
        self::$instagram->login();
//        return $instagram;

    }

    /**
     * @group getAccountByUsername
     */
    public function testGetAccountByUsername()
    {
        self::setUpInstagram();
        $account = self::$instagram->getAccount('kevin');
        $this->assertEquals('kevin', $account->getUsername());
        $this->assertEquals('3', $account->getId());
    }

    /**
     * @group getAccountById
     */
    public function testGetAccountById()
    {
        $i = $this->setUpInstagram();
        $account = $i->getAccountById(3);
        $this->assertEquals('kevin', $account->getUsername());
        $this->assertEquals('3', $account->getId());
    }

    public function testGetAccountByIdWithInvalidNumericId()
    {
        $i = $this->setUpInstagram();
        // PHP_INT_MAX is far larger than the greatest id so far and thus does not represent a valid account.

        $this->expectException(\InstagramScraper\Exception\InstagramNotFoundException::class);
        $i->getAccountById(PHP_INT_MAX);
    }

    /**
     * @group getMedias
     */
    public function testGetMedias()
    {
        $i = $this->setUpInstagram();
        $medias = $i->getMedias('kevin', 80);
        $this->assertEquals(80, sizeof($medias));
    }

    public function testGet100Medias()
    {
        $i = $this->setUpInstagram();
        $medias = $i->getMedias('kevin', 100);
        $this->assertEquals(100, sizeof($medias));
    }

    public function testGetMediasByTag()
    {
        $i = $this->setUpInstagram();
        $medias =$i->getMediasByTag('youneverknow', 20);
        $this->assertEquals(20, sizeof($medias));
    }

    public function testGetMediaByCode()
    {
        $i = $this->setUpInstagram();
        $media = $i->getMediaByCode('BHaRdodBouH');
        $this->assertEquals('kevin', $media->getOwner()->getUsername());
    }

    public function testGetMediaByUrl()
    {
        $i = $this->setUpInstagram();
        $media = $i->getMediaByUrl('https://www.instagram.com/p/BHaRdodBouH');
        $this->assertEquals('kevin', $media->getOwner()->getUsername());
    }

    /**
     * @group locationTopMedias
     */
    public function testGetLocationTopMediasById()
    {
        $i = $this->setUpInstagram();
        $medias = $i->getCurrentTopMediasByTagName(4);
        $this->assertGreaterThan(9, count($medias));
    }


    public function testGeMediaCommentsByCode()
    {
        $comments = self::$instagram->getMediaCommentsByCode('BR5Njq1gKmB', 40);
        //TODO: check why returns less comments
        $this->assertEquals(33, sizeof($comments));
    }

    /**
     * @group getUsernameById
     * @group auth
     */
    public function testGetUsernameById()
    {
        $i = $this->setUpInstagram();

        $username = $i->getUsernameById(3);
        $this->assertEquals('kevin', $username);
    }

    /**
     * @group getMediaLikesByCode
     */
    public function testGetMediaLikesByCode(){
        $i = $this->setUpInstagram();
        $likes = $i->getMediaLikesByCode('BR5Njq1gKmB', 10);
        print_r($likes);
    }
    
}