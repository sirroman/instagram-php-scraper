<?php

require '../vendor/autoload.php';

use InstagramScraper\Instagram;
use InstagramScraper\Model\Media;
use phpFastCache\CacheManager;
use PHPUnit\Framework\TestCase;


class InstagramTest extends TestCase
{
    private static $instagram;

    public static function setUpBeforeClass()
    {
        $sessionFolder = __DIR__ . DIRECTORY_SEPARATOR . 'sessions' . DIRECTORY_SEPARATOR;
        CacheManager::setDefaultConfig([
            'path' => $sessionFolder
        ]);
        $instanceCache = CacheManager::getInstance('files');
        self::$instagram = Instagram::withCredentials('raiym', 'youneverknow', $instanceCache);
        self::$instagram = Instagram::withCredentials('aniapetrova2414', 'p0o9u7y6', $instanceCache);
        self::$instagram->login();

    }

    public function testGetAccountByUsername()
    {
        $account = self::$instagram->getAccount('kevin');
        $this->assertEquals('kevin', $account->getUsername());
        $this->assertEquals('3', $account->getId());
    }

    /**
     * @group getAccountById
     */
    public function testGetAccountById()
    {

        $account = self::$instagram->getAccountById(3);
        $this->assertEquals('kevin', $account->getUsername());
        $this->assertEquals('3', $account->getId());
    }

    public function testGetAccountByIdWithInvalidNumericId()
    {
        // PHP_INT_MAX is far larger than the greatest id so far and thus does not represent a valid account.
        $this->expectException(\InstagramScraper\Exception\InstagramException::class);
        self::$instagram->getAccountById(PHP_INT_MAX);
    }

    /**
     * @group getMedias
     */
    public function testGetMedias()
    {
        $medias = self::$instagram->getMedias('kevin', 80);
        $this->assertEquals(80, sizeof($medias));
    }

    public function testGet100Medias()
    {
        $medias = self::$instagram->getMedias('kevin', 100);
        $this->assertEquals(100, sizeof($medias));
    }

    public function testGetMediasByTag()
    {
        $medias = self::$instagram->getMediasByTag('youneverknow', 20);
        $this->assertEquals(20, sizeof($medias));
    }

    public function testGetMediaByCode()
    {
        $media = self::$instagram->getMediaByCode('BHaRdodBouH');
        $this->assertEquals('kevin', $media->getOwner()->getUsername());
    }

    public function testGetMediaByUrl()
    {
        $media = self::$instagram->getMediaByUrl('https://www.instagram.com/p/BHaRdodBouH');
        $this->assertEquals('kevin', $media->getOwner()->getUsername());
    }

    /**
     * @group locationTopMedias
     */
    public function testGetLocationTopMediasById()
    {
        $medias = self::$instagram->getCurrentTopMediasByTagName(4);
        $this->assertGreaterThan(9, count($medias));
    }

    public function testGetLocationMediasById()
    {
        $medias = self::$instagram->getMediasByLocationId(1);
        $this->assertEquals(12, count($medias));
    }

    public function testGetLocationById()
    {
        $location = self::$instagram->getLocationById(1);
        $this->assertEquals('Dog Patch Labs', $location->getName());
    }

    public function testGetMediaByTag()
    {
        $medias = self::$instagram->getMediasByTag('hello');
        echo json_encode($medias);
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
        $username = self::$instagram->getUsernameById(3);
        $this->assertEquals('kevin', $username);
    }
    
    /**
     * @group getMediasByIserId
     */
    public function testGetMediasByUserId()
    {
        $instagram = new Instagram();
        $nonPrivateAccountMedias = $instagram->getMediasByUserId(3);
        $this->assertEquals(20, count($nonPrivateAccountMedias));
    }

    /**
     * @group accountPage
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
     */
    public function testGetMediaPageByUrl()
    {
        $instagram = new Instagram();
        $media = $instagram->getMediaByUrl('https://www.instagram.com/p/BHaRdodBouH');
        $this->assertEquals('kevin', $media->getOwner()->getUsername());
        $this->assertEquals(31, count($media->getComments()));
        $this->assertEquals(10, count($media->getLikes()));
    }

    /**
     * @group getNonAuthMediaByCodeSideCar
     */
    public function testGetMediaPageByUrlSlidecar()
    {
        // slidecar
        $instagram = new Instagram();
        $media = $instagram->getMediaByCode('Bgo1NmHFZaB');
        print_r($media);
        $this->assertEquals(Media::TYPE_SIDECAR, $media->getType());
        $this->assertEquals('beyonce', $media->getOwner()->getUsername());
        $this->assertGreaterThan(15, count($media->getComments()));
        $this->assertGreaterThan(10, count($media->getLikes()));
        $this->assertEquals(5, count($media->getSidecarMedias()));

    }

    // TODO: Add test getMediaById
    // TODO: Add test getLocationById
}