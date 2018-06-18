<?php

require '../vendor/autoload.php';

use InstagramScraper\Instagram;
use InstagramScraper\Model\Media;
use phpFastCache\CacheManager;
use PHPUnit\Framework\TestCase;


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


    public function testGetLocationMediasById()
    {
        $i = $this->setUpInstagram();
        $medias = $i->getMediasByLocationId(1);
        $this->assertEquals(12, count($medias));
    }

    /**
     * @group getLocationById
     * @group noAuth
     */
    public function testGetLocationById()
    {
        $i = new Instagram();
        $location =$i->getLocationById(1032158659);
        $this->assertEquals('Публичная библиотека. Центр культурных программ', $location->getName());
//        $this->assertEquals(200, $this->getHttpCode($location->getProfilePicUrl()));
        $this->assertEquals(39.7263,$location->getLng());
        $this->assertEquals(47.22837, $location->getLat());
//        print_r($location);
    }


    /**
     * @group getMediasByLocationId
     * @group noAuth
     */
    public function testGetMediasByLocationId()
    {

        $i = new Instagram();

        $location =$i->getMediasByLocationId(1032158659);

        $this->assertEquals('Публичная библиотека. Центр культурных программ', $location->getName());
        $this->assertEquals(200, $this->getHttpCode($location->getProfilePicUrl()));
        $this->assertEquals(39.7263, $location->getLng());
        $this->assertEquals(47.22837,$location->getLat());
    }


    public function testGetIdFromCode()
    {
        $code = Media::getCodeFromId('1270593720437182847');
        $this->assertEquals('BGiDkHAgBF_', $code);
        $code = Media::getCodeFromId('1270593720437182847_3');
        $this->assertEquals('BGiDkHAgBF_', $code);
        $code = Media::getCodeFromId(1270593720437182847);
        $this->assertEquals('BGiDkHAgBF_', $code);
    }

    public function testGetCodeFromId()
    {
        $id = Media::getIdFromCode('BGiDkHAgBF_');
        $this->assertEquals(1270593720437182847, $id);
    }

    public function testGeMediaCommentsByCode()
    {
        $comments = self::$instagram->getMediaCommentsByCode('BR5Njq1gKmB', 40);
        //TODO: check why returns less comments
        $this->assertEquals(33, sizeof($comments));
    }
    
    /**
     * @group getUsernameById
     */
    public function testGetUsernameById()
    {
        $i = $this->setUpInstagram();

        $username = $i->getUsernameById(3);
        $this->assertEquals('kevin', $username);
    }
    
    /**
     * @group getMediasByIserId
     * @group noAuth
     */
    public function testGetMediasByUserId()
    {
        $instagram = new Instagram();
        $nonPrivateAccountMedias = $instagram->getMediasByUserId(3);
        $this->assertEquals(12, count($nonPrivateAccountMedias));

        $nonPrivateAccountMedias = $instagram->getMediasByUserId(3, 50);
        $this->assertEquals(50, count($nonPrivateAccountMedias));
    }

    /**
     * @group accountPage
     * @group noAuth
     * @throws \InstagramScraper\Exception\InstagramException
     * @throws \InstagramScraper\Exception\InstagramNotFoundException
     */
    public function testGetAccountPage()
    {
        $instagram = new Instagram();
        $accountPage = $instagram->getAccountPage('kevin');
        $this->assertEquals(3,$accountPage->account->getId());
        $this->assertEquals(12, count($accountPage->medias));
    }


    /**
     * @group getNonAuthMediaByUrl
     * @group noAuth
     */
    public function testGetMediaPageByUrl()
    {
        $instagram = new Instagram();
        $media = $instagram->getMediaByUrl('https://www.instagram.com/p/BHaRdodBouH');
        $this->assertEquals('kevin', $media->getOwner()->getUsername());
        $this->assertGreaterThan(20, count($media->getComments()));
        $this->assertEquals(10, count($media->getLikes()));
    }

    /**
     * @group getNonAuthMediaByCodeSideCar
     * @group noAuth
     */
    public function testGetMediaPageByUrlSlidecar()
    {
        $instagram = new Instagram();
        $media = $instagram->getMediaByCode('Bgo1NmHFZaB');
        $this->assertEquals(Media::TYPE_SIDECAR, $media->getType());
        $this->assertEquals('beyonce', $media->getOwner()->getUsername());
        $this->assertGreaterThan(15, count($media->getComments()));
        $this->assertGreaterThan(5, count($media->getLikes()));
        $this->assertEquals(5, count($media->getSidecarMedias()));

    }

    /**
     * @group getNonAuthMediaByCodeVideo
     * @group noAuth
     */
    public function testGetMediaPageByUrlVideo()
    {
        $instagram = new Instagram();
        $media = $instagram->getMediaById(1733446317571985644);
        $this->assertEquals(Media::TYPE_VIDEO, $media->getType());
        $this->assertEquals(200, $this->getHttpCode($media->getVideoStandardResolutionUrl()));
        $this->assertEquals('beyonce', $media->getOwner()->getUsername());
        $this->assertGreaterThan(15, count($media->getComments()));
        $this->assertGreaterThan(5, count($media->getLikes()));
        $this->assertEquals(0, count($media->getSidecarMedias()));

    }

    /**
     * @group getNoAuthMediasByTag
     * @group noAuth
     */
    public function testNoAuthGetMediasByTag(){
        $instagram = new Instagram();
        $medias = $instagram->getMediasByTag('bnw',30);
        $this->assertEquals(30, count($medias));
    }


    protected function getHttpCode($url) {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_exec($handle);
        $httpCode= curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        return $httpCode;
    }
    // TODO: Add test getMediaById
    // TODO: Add test getLocationById
}