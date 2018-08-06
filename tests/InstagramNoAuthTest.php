<?php

require '../vendor/autoload.php';

use InstagramScraper\Instagram;
use InstagramScraper\Model\Media;
use phpFastCache\CacheManager;
use PHPUnit\Framework\TestCase;

/**
 * Class InstagramTest
 * @group noAuth
 */
class InstagramTest extends TestCase
{

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
        $this->assertEquals(39.7263, $location->getLng());
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

        $medias =$i->getMediasByLocationId(1032158659);
        $this->assertEquals(12, count($medias));
        $this->assertGreaterThan(1000000000, $medias[0]->getId());
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
//        print_r($media);
        $this->assertEquals('kevin', $media->getOwner()->getUsername());
        $this->assertGreaterThan(20, count($media->getComments()));
        // @TODO проверить позже может появятся лайки в будущем?
        //$this->assertEquals(10, count($media->getLikes()));
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
        //$this->assertGreaterThan(5, count($media->getLikes()));
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
        //$this->assertGreaterThan(5, count($media->getLikes()));
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

    /**
     * @group tagPage
     * @throws \InstagramScraper\Exception\InstagramException
     */
    public function testGetTagPage(){
        $instagram = new Instagram();
        $tagPage = $instagram->getTagPage('bnw',30);
        $this->assertGreaterThan(200000, $tagPage->count);
        $this->assertInstanceOf(Media::class, array_pop($tagPage->medias));
        $this->assertInstanceOf(Media::class, array_pop($tagPage->topMedias));
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