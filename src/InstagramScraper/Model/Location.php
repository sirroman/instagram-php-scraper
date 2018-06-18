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
        'directory' => 'initDirectory'
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

    protected function initLocationMedias($value, $prop, $props)
    {

//        print_r($value);
        $this->mediaCount = $value['count'];
    }

    protected function initDirectory($value, $prop, $props){
            $this->countryId    = $value['country']['id'];
            $this->countryName  = $value['country']['name'];
            $this->cityId       = $value['city']['id'];
            $this->cityName     = $value['city']['name'];
    }
}
