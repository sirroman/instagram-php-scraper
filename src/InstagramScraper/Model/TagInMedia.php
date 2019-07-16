<?php


namespace InstagramScraper\Model;


class TagInMedia extends AbstractModel
{

    /**
     * @var float
     */
    protected $x;

    /**
     * @var float
     */
    protected $y;

    /**
     * @var Account
     */
    protected $user;

    /**
     * @return float
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * @return float
     */
    public function getY(): float
    {
        return $this->y;
    }

    /**
     * @return Account
     */
    public function getUser(): Account
    {
        return $this->user;
    }

    /**
     * @param $value
     * @param $prop
     */
    protected function initPropertiesCustom($value, $prop, $arr)
    {
        switch ($prop) {
            case 'x':
                $this->x = $value;
                break;
            case 'y':
                $this->y = $value;
                break;
            case 'user':
                $this->user = Account::create($value);
                break;

            default:
                $this->data[$prop] = $value;
        }
    }

}