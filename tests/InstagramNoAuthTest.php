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
     * @group getLocationPage
     * @group noAuth
     */
    public function testGetMediasByLocationId()
    {

        $i = new Instagram();

        $r =$i->getLocationPage(1032158659);
        $this->assertEquals(1032158659, $r->location->getId());
        $this->assertEquals('Публичная библиотека. Центр культурных программ', $r->location->getName());
        $this->assertEquals(47.22837, $r->location->getLat());
        $this->assertEquals(39.7263, $r->location->getLng());
        $this->assertEquals('Донская государственная публичная библиотека - один из крупных культурных центров г. Ростова-на-Дону. ', $r->location->getBlurb());
        $this->assertEquals('http://www.dspl.ru/', $r->location->getWebsite());
        $this->assertEquals('8(863)2640600', $r->location->getPhone());
        $address = json_decode('{"street_address": "\u0420\u043e\u0441\u0442\u043e\u0432-\u043d\u0430-\u0414\u043e\u043d\u0443, \u041f\u0443\u0448\u043a\u0438\u043d\u0441\u043a\u0430\u044f, 175 \u0410", "zip_code": "344000", "city_name": "Rostovnadonu, Rostovskaya Oblast\', Russia", "region_name": "", "country_code": "RU"}');
        $this->assertEquals($address, $r->location->getAddress());
        $this->assertEquals('https://scontent-frt3-2.cdninstagram.com/vp/70f92aa1e46822db064d3434dc4f0922/5C3D1556/t51.2885-15/e35/c0.32.911.911/s150x150/42078547_294834521244918_6897421412395384832_n.jpg', $r->location->getProfilePicUrl());

        $this->assertGreaterThan(26260, $r->location->getMediaCount());
        $this->assertGreaterThan(10, count($r->medias));
        $this->assertGreaterThan(5, count($r->topMedias));
        $this->assertInstanceOf(Media::class, $r->medias[0]);
        $this->assertInstanceOf(Media::class, $r->topMedias[0]);

        $r =$i->getLocationPage(1032158659, $r->medias[count($r->medias)-1]->getId());
        $this->assertGreaterThan(10, count($r->medias));

    }


    /**
         * @group getMediasByUserId
     * @group noAuth
     */
    public function testGetMediasByUserId()
    {
        $instagram = new Instagram();
        $response = $instagram->getMediasByUserId(3, 13);

        $this->assertEquals(13, count($response->medias));
        $this->assertEquals(1, $response->pageInfo->has_next_page);
        $this->assertGreaterThan(1600, $response->count);


        $response = $instagram->getMediasByUserId(3, 13, $response->pageInfo->end_cursor);
        $this->assertEquals(13, count($response->medias));
        $this->assertEquals(1, $response->pageInfo->has_next_page);
        $this->assertGreaterThan(1600, $response->count);
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

        $response = $instagram->getMediasByUserId($accountPage->account->getId(), 12, $accountPage->pageInfo->end_cursor);
        $this->assertEquals(12, count($response->medias));
        $this->assertEquals(1, $response->pageInfo->has_next_page);
        $this->assertGreaterThan(1600, $response->count);

    }


    /**
     * @group getNonAuthMediaByUrl
     * @group noAuth
     */
    public function testGetMediaPageByUrl()
    {
        sleep(1);
        $instagram = new Instagram();
        $media = $instagram->getMediaByUrl('https://www.instagram.com/p/BHaRdodBouH');
//        print_r($media);
        $this->assertEquals('kevin', $media->getOwner()->getUsername());
        $this->assertGreaterThan(20, count($media->getComments()));
        // @TODO проверить позже может появятся лайки в будущем?
        //$this->assertEquals(10, count($media->getLikes()));
        $this->assertNull($media->getOwner()->getMediaCount());
        $this->assertNull($media->getOwner()->getFollowedByCount());
        $this->assertNull($media->getOwner()->getFollowsCount());
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
        $this->assertGreaterThan(5, count($media->getComments()));
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

        sleep(1);
        $instagram = new Instagram();
        $medias = $instagram->getMediasByTag('bnw',30);
        $this->assertEquals(30, count($medias));
    }

    /**
     * @group tagPage
     * @throws \InstagramScraper\Exception\InstagramException
     */
    public function testGetTagPage(){
        sleep(3);
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