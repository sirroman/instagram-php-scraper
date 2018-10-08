<?php

namespace InstagramScraper\Model;


class Like extends AbstractModel
{
    /**
     * @var
     */
    protected $id;

    /**
     * @var Account
     */
    protected $username;

    /**
     * @var string
     */
    protected $full_name;

    /**
     * @var string
     */
    protected $profile_pic_url;

    /**
     * @var bool
     */
    protected $is_verified;

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
    public function getUserName()
    {
        return $this->username;
    }

    /**
     * @param $value
     * @param $prop
     */
    protected function initPropertiesCustom($value, $prop)
    {
        switch ($prop) {
            case 'id':
                $this->id = $value;
                break;
            case 'username':
                $this->username = $value;
                break;
            case 'full_name':
                $this->full_name = $value;
                break;

            case 'profile_pic_url':
                $this->profile_pic_url = $value;
                break;

            case 'is_verified':
                $this->is_verified = $value;
                break;
        }
    }

}