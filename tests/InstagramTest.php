<?php

namespace InstagramScraper\Tests;

use InstagramScraper\Instagram;
use InstagramScraper\Model\Media;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Helper\Psr16Adapter;
use PHPUnit\Framework\TestCase;


/**
 * Class InstagramTest
 * @group auth
 */
class InstagramTest extends TestCase
{
    /**
     * @var Instagram
     */
    private static $instagram;

    private static $username;
    private static $password;

    public static function setUpBeforeClass() : void
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
        $defaultDriver = 'Files';
        $options = new ConfigurationOption([
            'path' => $sessionFolder
        ]);
        $instanceCache = new Psr16Adapter($defaultDriver, $options);

        self::$instagram = Instagram::withCredentials($_ENV['LOGIN'], $_ENV['PASSWORD'], $instanceCache);

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
        $this->expectExceptionMessage('Failed to fetch account with given id');
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
        $medias = self::$instagram->getMediasByLocationId(881442298, 56);
        $this->assertEquals(56, count($medias));
    }

    public function testGetLocationById()
    {
        $location = self::$instagram->getLocationById(881442298);
        $this->assertEquals('Pendleberry Grove', $location->getName());
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
        $this->assertLessThanOrEqual(40, sizeof($comments));
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
    

    public function testGetMediasByUserId()
    {
        $instagram = new Instagram();
        $nonPrivateAccountMedias = $instagram->getMediasByUserId(3);
        $this->assertEquals(12, count($nonPrivateAccountMedias));
    }

    public function testLikeMediaById()
    {
        // https://www.instagram.com/p/B910VxfgEIO/
        self::$instagram->like('2266948182120350222');
        $this->assertTrue(true, 'Return type ensures this assertion is never reached on failure');
    }

    public function testUnlikeMediaById()
    {
        // https://www.instagram.com/p/B910VxfgEIO/
        self::$instagram->unlike('2266948182120350222');
        $this->assertTrue(true, 'Return type ensures this assertion is never reached on failure');
    }

    public function testAddAndDeleteComment()
    {
        // https://www.instagram.com/p/B910VxfgEIO/
        $comment1 = self::$instagram->addComment('2266948182120350222', 'Cool!');
        $this->assertInstanceOf('InstagramScraper\Model\Comment', $comment1);

        $comment2 = self::$instagram->addComment('2266948182120350222', '+1', $comment1);
        $this->assertInstanceOf('InstagramScraper\Model\Comment', $comment2);

        self::$instagram->deleteComment('2266948182120350222', $comment2);
        $this->assertTrue(true, 'Return type ensures this assertion is never reached on failure');

        self::$instagram->deleteComment('2266948182120350222', $comment1);
        $this->assertTrue(true, 'Return type ensures this assertion is never reached on failure');
    }

    /**
     * @group getPaginateMediasByLocationId
     */
    public function testGetPaginateMediasByLocationId()
    {
        $medias = self::$instagram->getPaginateMediasByLocationId('201176299974017');
        echo json_encode($medias);
    }
    // TODO: Add test getMediaById
    // TODO: Add test getLocationById
}
