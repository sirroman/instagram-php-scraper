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
    const SLEEP =2;

    /**
     * @group getLocationById
     */
    public function testGetLocationById()
    {
        sleep(self::SLEEP);
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
        $address = json_decode('{"street_address": "\u0420\u043e\u0441\u0442\u043e\u0432-\u043d\u0430-\u0414\u043e\u043d\u0443, \u041f\u0443\u0448\u043a\u0438\u043d\u0441\u043a\u0430\u044f, 175 \u0410", "zip_code": "344000", "city_name": "Rostovnadonu, Rostovskaya Oblast\', Russia", "region_name": "", "country_code": "RU", "exact_city_match" : false, "exact_region_match" : false, "exact_country_match" : false}');
        $this->assertEquals($address, $r->location->getAddress());

        $this->assertGreaterThan(30,strlen( $r->location->getProfilePicUrl()));
        $this->assertGreaterThan(26260, $r->location->getMediaCount());
        $this->assertGreaterThan(10, count($r->medias));
        $this->assertGreaterThan(5, count($r->topMedias));
        $this->assertInstanceOf(Media::class, $r->medias[0]);
        $this->assertInstanceOf(Media::class, $r->topMedias[0]);
        $this->assertGreaterThan(3,$r->topMedias[0]->getOwnerId());
        $this->assertGreaterThan(3,$r->medias[0]->getOwnerId());

        $r =$i->getLocationPage(1032158659, $r->medias[count($r->medias)-1]->getId());
        $this->assertGreaterThan(10, count($r->medias));


        // checking that Instagram did not add new interesting properties
        $unparsed = $r->location->getUnparsed();
        unset ($unparsed['primary_alias_on_fb']);
        $this->assertEquals(0, count ($unparsed));

    }


    /**
         * @group getMediasByUserId
     * @group noAuth
     */
    public function testGetMediasByUserId()
    {
        $instagram = new Instagram();
        $response = $instagram->getMediasByUserId(3, 13);
//print_r($response->medias[0]); exit();
        $this->assertEquals(13, count($response->medias));
        $this->assertEquals(1, $response->pageInfo->has_next_page);
        $this->assertGreaterThan(1600, $response->count);
        $this->assertEquals(3, $response->medias[0]->getOwnerId());
        $this->assertEquals(3, $response->medias[0]->getOwner()->getId());
        $this->assertGreaterThan(20, strlen($response->medias[0]->getImageHighResolutionUrl()));
        $this->assertGreaterThan(20, strlen($response->medias[0]->getImageThumbnailUrl()));
        $this->assertEquals(0, strlen($response->medias[0]->getImageStandardResolutionUrl()));
        $this->assertEquals(0, strlen($response->medias[0]->getImageLowResolutionUrl()));

        $countSquare = 0;
        foreach ($response->medias[0]->getSquareImages() as $key => $squareImage) {
            $this->assertGreaterThan(20, strlen($squareImage));
            $countSquare++;
        }
        $this->assertGreaterThan(3, $countSquare);

        // checking that Instagram did not add new interesting properties
        $unparsed = $response->medias[0]->getUnparsed();
        unset ($unparsed['comments_disabled']);
        unset ($unparsed['dimensions']);
        unset ($unparsed['gating_info']);
        unset ($unparsed['media_preview']);
//        print_r($unparsed);
        $this->assertEquals(0, count ($unparsed));


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

        // checking that Instagram did not add new interesting properties
        $unparsed = $accountPage->account->getUnparsed();
//        print_r($unparsed);
        $this->assertEquals('', $accountPage->account->getBusinessEmail());
        $this->assertFalse($accountPage->account->isHasChannel());
        $this->assertNull($accountPage->account->getConnectedFbPage());
        $this->assertEquals(0, $unparsed['edge_felix_video_timeline']['count']);
        $this->assertFalse($accountPage->account->isBlockedByViewer());
        $this->assertFalse($accountPage->account->isCountryBlock());
        $this->assertFalse($accountPage->account->isFollowedByViewer());
        $this->assertFalse($accountPage->account->isFollowsViewer());
        $this->assertFalse($accountPage->account->isHasBlockedViewer());
        $this->assertEquals(0, $accountPage->account->getHighlightReelCount());
        $this->assertFalse($accountPage->account->isHasRequestedViewer());
        $this->assertFalse($accountPage->account->isBusinessAccount());
        $this->assertFalse($accountPage->account->isJoinedRecently());
        $this->assertEquals('', $accountPage->account->getBusinessCategoryName());
        $this->assertEquals('', $accountPage->account->getBusinessPhoneNumber());
        $this->assertEquals('{}',$accountPage->account->getBusinessAddressJson());
        $this->assertFalse($accountPage->account->isRequestedByViewer());
        $this->assertEquals(3, $accountPage->medias[1]->getOwnerId());

//        $this->assertGreaterThan(10, strlen($accountPage->rhxGis));


        unset ($unparsed['external_url_linkshimmed']);
        unset ($unparsed['edge_mutual_followed_by']);
        unset ($unparsed['edge_felix_video_timeline']);
        unset ($unparsed['edge_saved_media']);
        unset ($unparsed['edge_media_collections']);
        $this->assertEquals(0, count ($unparsed));



        $unparsed = $accountPage->medias[0]->getUnparsed();
//        print_r($unparsed);
        unset ($unparsed['comments_disabled']);
        unset ($unparsed['dimensions']);
        unset ($unparsed['gating_info']);
        unset ($unparsed['media_preview']);
        unset ($unparsed['accessibility_caption']);
//        print_r($unparsed);
        $this->assertEquals(0, count ($unparsed));
//        print_r($accountPage->medias[0]);
//        $instagram->setRhxGis($accountPage->rhxGis);
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
        sleep(self::SLEEP);
        $instagram = new Instagram();
        $media = $instagram->getMediaByUrl('https://www.instagram.com/p/BHaRdodBouH');
//print_r($media);
        $this->assertEquals('kevin', $media->getOwner()->getUsername());
        $this->assertEquals(3, $media->getOwner()->getId());
        $this->assertGreaterThan(20, count($media->getComments()));
        // @TODO проверить позже может появятся лайки в будущем?
        $this->assertEquals(0, count($media->getLikes()));
        $this->assertNull($media->getOwner()->getMediaCount());
        $this->assertNull($media->getOwner()->getFollowedByCount());
        $this->assertNull($media->getOwner()->getFollowsCount());
//        $this->assertGreaterThan(time()-5, $media->get)
        $this->assertGreaterThan(20, strlen($media->getImageHighResolutionUrl()));
        $this->assertGreaterThan(20, strlen($media->getImageStandardResolutionUrl()));
        $this->assertGreaterThan(20, strlen($media->getImageLowResolutionUrl()));
        $this->assertGreaterThan(20, strlen($media->getImageThumbnailUrl()));

        $countSquare = 0;
        foreach ($media->getSquareImages() as $key => $squareImage) {
            $this->assertGreaterThan(20, strlen($squareImage));
            $countSquare++;
        }
        // getMediaByUrl does not return small images
        $this->assertEquals(0, $countSquare);

        $this->assertEquals('No photo description available.', $media->getAccessibilityCaption());
        $this->assertGreaterThan(100, strlen($media->getPreview()));
        $this->assertEquals(['height'=>1081, 'width'=>1080], $media->getDimensions());
        $this->assertEquals(null, $media->getVideoDuration());

        $this->assertEquals(0, count($media->getTaggedUsers()));

        // check what instagram did not add new interesting properties
        $unparsed = $media->getUnparsed();

        unset($unparsed['gating_info']);
        unset($unparsed['should_log_client_event']);
        unset($unparsed['tracking_token']);
        unset($unparsed['comments_disabled']);
        unset($unparsed['edge_media_to_sponsor_user']);
        unset($unparsed['viewer_has_liked']);
        unset($unparsed['viewer_has_saved']);
        unset($unparsed['viewer_has_saved_to_collection']);
        unset($unparsed['viewer_in_photo_of_you']);
        unset($unparsed['viewer_can_reshare']);
        unset($unparsed['has_ranked_comments']);
        unset($unparsed['edge_web_media_to_related_media']);
        unset($unparsed['encoding_status']);
        unset($unparsed['is_published']);
        unset($unparsed['dash_info']);
        unset($unparsed['edge_web_media_to_related_media']);
        unset($unparsed['product_type']);

//        print_r($unparsed);
        $this->assertEquals(0, count($unparsed));

    }

    /**
     * @group getNonAuthMediaByCodeSideCar
     * @group noAuth
     */
    public function testGetMediaPageByUrlSlidecar()
    {
        sleep(self::SLEEP);
        $instagram = new Instagram();
        $media = $instagram->getMediaByCode('Bgo1NmHFZaB');
        $this->assertEquals(Media::TYPE_SIDECAR, $media->getType());
        $this->assertEquals('beyonce', $media->getOwner()->getUsername());
        $this->assertGreaterThan(5, count($media->getComments()));
        $this->assertEquals(0, count($media->getLikes()));
        $this->assertEquals(5, count($media->getSidecarMedias()));
        $this->assertGreaterThan(20, strlen($media->getImageHighResolutionUrl()));
        $this->assertGreaterThan(20, strlen($media->getImageStandardResolutionUrl()));
        $this->assertGreaterThan(20, strlen($media->getImageLowResolutionUrl()));
        $this->assertGreaterThan(20, strlen($media->getImageThumbnailUrl()));

        $countSquare = 0;
        foreach ($media->getSquareImages() as $key => $squareImage) {
            $this->assertGreaterThan(20, strlen($squareImage));
        }
        // getMediaByUrl does not return small images
        $this->assertEquals(0, $countSquare);

    }

    /**
     * @group getNonAuthMediaByCodeVideo
     * @group noAuth
     */
    public function testGetMediaPageByUrlVideo()
    {
        sleep(self::SLEEP);
        $instagram = new Instagram();
        $media = $instagram->getMediaById(1733446317571985644);
//        print_r($media);
        $this->assertEquals(Media::TYPE_VIDEO, $media->getType());
        $this->assertEquals(200, $this->getHttpCode($media->getVideoStandardResolutionUrl()));
        $this->assertEquals('beyonce', $media->getOwner()->getUsername());
        $this->assertGreaterThan(15, count($media->getComments()));
        $this->assertEquals(0, count($media->getLikes()));
        $this->assertEquals(0, count($media->getSidecarMedias()));
        $this->assertGreaterThan(20, strlen($media->getImageHighResolutionUrl()));
        $this->assertGreaterThan(20, strlen($media->getImageStandardResolutionUrl()));
        $this->assertGreaterThan(20, strlen($media->getImageLowResolutionUrl()));
        $this->assertGreaterThan(20, strlen($media->getImageThumbnailUrl()));

        $countSquare = 0;
        foreach ($media->getSquareImages() as $key => $squareImage) {
            $this->assertGreaterThan(20, strlen($squareImage));
        }
        // getMediaByUrl does not return small images
        $this->assertEquals(0, $countSquare);


        $this->assertEquals(null, $media->getAccessibilityCaption());
        $this->assertGreaterThan(100, strlen($media->getPreview()));
        $this->assertEquals(['height'=>612, 'width'=>612], $media->getDimensions());
        $this->assertEquals(45, $media->getVideoDuration());

        $this->assertEquals(0, count($media->getTaggedUsers()));

        // check what instagram did not add new interesting properties
        $unparsed = $media->getUnparsed();

        unset($unparsed['gating_info']);
        unset($unparsed['should_log_client_event']);
        unset($unparsed['tracking_token']);
        unset($unparsed['comments_disabled']);
        unset($unparsed['edge_media_to_sponsor_user']);
        unset($unparsed['viewer_has_liked']);
        unset($unparsed['viewer_has_saved']);
        unset($unparsed['viewer_has_saved_to_collection']);
        unset($unparsed['viewer_in_photo_of_you']);
        unset($unparsed['viewer_can_reshare']);
        unset($unparsed['has_ranked_comments']);
        unset($unparsed['edge_web_media_to_related_media']);
        unset($unparsed['encoding_status']);
        unset($unparsed['is_published']);
        unset($unparsed['dash_info']);
        unset($unparsed['edge_web_media_to_related_media']);
        unset($unparsed['product_type']);

//        print_r($unparsed);
        $this->assertEquals(0, count($unparsed));

    }

    /**
     * @group getNonAuthMediaWithUsers
     * @group noAuth
     */
    public function testGetMediaPageByCodeWithUsers()
    {
        sleep(self::SLEEP);
        $instagram = new Instagram();
        $media = $instagram->getMediaByCode('Bz2xf5KBOMN');
//        print_r($media);
//print_r($media->getUnparsed());

        $this->assertEquals('adamandeve_hotel', $media->getOwner()->getUsername());
        $this->assertEquals(1351600589, $media->getOwner()->getId());
        $this->assertGreaterThan(20, count($media->getComments()));
        // @TODO проверить позже может появятся лайки в будущем?
        $this->assertEquals(0, count($media->getLikes()));
        $this->assertNull($media->getOwner()->getMediaCount());
        $this->assertNull($media->getOwner()->getFollowedByCount());
        $this->assertNull($media->getOwner()->getFollowsCount());
//        $this->assertGreaterThan(time()-5, $media->get)
        $this->assertGreaterThan(20, strlen($media->getImageHighResolutionUrl()));
        $this->assertGreaterThan(20, strlen($media->getImageStandardResolutionUrl()));
        $this->assertGreaterThan(20, strlen($media->getImageLowResolutionUrl()));
        $this->assertGreaterThan(20, strlen($media->getImageThumbnailUrl()));

        $countSquare = 0;
        foreach ($media->getSquareImages() as $key => $squareImage) {
            $this->assertGreaterThan(20, strlen($squareImage));
            $countSquare++;
        }
        // getMediaByUrl does not return small images
        $this->assertEquals(0, $countSquare);


        $this->assertEquals('Image may contain: one or more people, sky, outdoor and water', $media->getAccessibilityCaption());
        $this->assertGreaterThan(100, strlen($media->getPreview()));
        $this->assertEquals(['height'=>1080, 'width'=>1080], $media->getDimensions());

        $this->assertEquals(11, count($media->getTaggedUsers()));
        $this->assertInstanceOf(\InstagramScraper\Model\Account::class, $media->getTaggedUsers()[0]->getUser());

        // check what instagram did not add new interesting properties
        $unparsed = $media->getUnparsed();

        unset($unparsed['gating_info']);
        unset($unparsed['should_log_client_event']);
        unset($unparsed['tracking_token']);
        unset($unparsed['comments_disabled']);
        unset($unparsed['edge_media_to_sponsor_user']);
        unset($unparsed['viewer_has_liked']);
        unset($unparsed['viewer_has_saved']);
        unset($unparsed['viewer_has_saved_to_collection']);
        unset($unparsed['viewer_in_photo_of_you']);
        unset($unparsed['viewer_can_reshare']);
        unset($unparsed['has_ranked_comments']);
        unset($unparsed['edge_web_media_to_related_media']);

//        print_r($unparsed);
        $this->assertEquals(0, count($unparsed));


    }


    /**
     * @group getNoAuthMediasByTag
     * @group noAuth
     */
    public function testNoAuthGetMediasByTag(){

        sleep(self::SLEEP);
        $instagram = new Instagram();
        $medias = $instagram->getMediasByTag('bnw',30);
        $this->assertEquals(30, count($medias));
    }

    /**
     * @group tagPage
     */
    public function testGetTagPage(){
        sleep(self::SLEEP);
        $instagram = new Instagram();
        $tagPage = $instagram->getTagPage('bnw',30);
        $this->assertGreaterThan(200000, $tagPage->count);
//        print_r($tagPage);
        $this->assertInstanceOf(Media::class, $tagPage->medias[0]);
        $mediaTop =  array_pop($tagPage->topMedias);
        $this->assertInstanceOf(Media::class, $mediaTop);
        $this->assertGreaterThan(3, $mediaTop->getOwnerId());
        $this->assertGreaterThan(3, $tagPage->medias[0]->getOwnerId());

    }

    /**
     * @group highlightReels
     */
    public function testGetReels(){
        $instagram = new Instagram();
        $r = $instagram->getHighlighReels(309893914);

        $this->assertGreaterThan(2, count($r->stories));
        $this->assertGreaterThan(1000000, $r->stories[0]->getId());
        $this->assertEquals(Media::TYPE_HIGHLIGHT_REEL, $r->stories[0]->getType());

        $this->assertGreaterThan(1, strlen($r->stories[0]->getCaption()));

        $this->assertEquals(200, $this->getHttpCode($r->stories[0]->getImageHighResolutionUrl()));
        $this->assertEquals(200, $this->getHttpCode($r->stories[0]->getImageLowResolutionUrl()));
        $this->assertTrue($r->hasPublicStory);

        $this->assertInstanceOf(\InstagramScraper\Model\Account::class, $r->account);
        $this->assertEquals(309893914, $r->account->getId());
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