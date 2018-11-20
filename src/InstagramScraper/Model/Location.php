<?php

namespace InstagramScraper\Model;


class Location extends AbstractModel
{
    /**
     * @var array
     */
    protected static $initPropertiesMap = [
        'id' => 'id',
        'has_public_page' => 'hasPublicPage',
        'name' => 'name',
        'slug' => 'slug',
        'lat' => 'lat',
        'lng' => 'lng',
        'modified' => 'modified',
        'profile_pic_url' => 'profilePicUrl',
        'edge_location_to_media' => 'initLocationMedias',
        'edge_location_to_top_posts' => 'initLocationTopMedias',
        'directory' => 'initDirectory',
        'phone' => 'phone',
        'website' => 'website',
        'blurb' => 'blurb',
        'address_json' => 'initAddress'
    ];
    /**
     * @var
     */
    protected $id;
    /**
     * @var
     */
    protected $hasPublicPage;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $slug;
    /**
     * @var
     */
    protected $lng;
    /**
     * @var
     */
    protected $lat;
    /**
     * @var bool
     */
    protected $isLoaded = false;

    /**
     * @var 
     */
    protected $modified;

    /**
     * @var string
     */
    protected $profilePicUrl;

    /**
     * @var int
     */
    protected $mediaCount;

    protected $countryId;
    protected $countryName;

    protected $cityId;
    protected $cityName;

    /**
     * @var string
     */
    protected $blurb;

    /**
     * @var string
     */
    protected $website;

    /**
     * @var string
     */
    protected $phone;


    /**
     * @var array
     */
    protected $address=[];

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getHasPublicPage()
    {
        return $this->hasPublicPage;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return mixed
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @return string
     */
    public function getProfilePicUrl(){
        return $this->profilePicUrl;
    }

    /**
     * @return string
     */
    public function getMediaCount(){
        return $this->mediaCount;
    }

    /**
     * @return string
     */
    public function getCountryId(){
        return $this->countryId;
    }

    /**
     * @return string
     */
    public function getCountryName(){
        return $this->countryName;
    }


    /**
     * @return string
     */
    public function getCityId(){
        return $this->cityId;
    }

    /**
     * @return string
     */
    public function getCityName(){
        return $this->cityName;
    }

    public function getBlurb(){
        return $this->blurb;
    }

    public function getWebsite(){
        return $this->website;
    }

    public function getPhone(){
        return $this->phone;
    }

    public function getAddress(){
        return $this->address;
    }

    protected function initLocationMedias($value, $prop, $props)
    {
//        print_r($value);
        $this->mediaCount = $value['count'];
    }

    protected function initLocationTopMedias($value, $prop, $props)
    {

    }

    protected function initDirectory($value, $prop, $props){
            $this->countryId    = $value['country']['id'];
            $this->countryName  = $value['country']['name'];
            $this->cityId       = $value['city']['id'];
            $this->cityName     = $value['city']['name'];
    }

    protected function initAddress($value, $prop, $props){
        $this->address = json_decode($value);
    }
}
