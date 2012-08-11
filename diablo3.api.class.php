<?php
class Diablo3 {
    private $battlenet_tag;
    private $protocol      = 'http://';
    private $host          = 'us.battle.net';
    private $followerTypes = array('enchantress', 'templar', 'scoundrel');
    private $artisanTypes  = array('blacksmith', 'jeweler');
    private $career_url;
    private $hero_url;
    private $item_url;
    private $follower_url;
    private $artisan_url;

    public function __construct($battlenet_tag) {
        if($battlenet_tag !== '') {
            $this->battlenet_tag = (string)$battlenet_tag;
            $this->career_url    = $this->protocol.$this->host.'/api/d3/profile/'.$this->battlenet_tag.'/';
            $this->hero_url      = $this->protocol.$this->host.'/api/d3/profile/'.$this->battlenet_tag.'/hero/';
        } else {
            error_log("Required Battle.net tag on line ".__LINE__." in file ".__FILE__);
            die();
        }

        $this->item_url      = $this->protocol.$this->host.'/api/d3/data/item/';
        $this->follower_url  = $this->protocol.$this->host.'/api/d3/data/follower/';
        $this->artisan_url   = $this->protocol.$this->host.'/api/d3/data/artisan/';
    }

    private function cURLcheckBasics() {
        if(!function_exists("curl_init") &&
           !function_exists("curl_setopt") &&
           !function_exists("curl_exec") &&
           !function_exists("curl_close")) return false;
        else return true;
    }

    private function getData($url) {
        if(!$this->cURLcheckBasics()) { return false; }
        if($url == '') { return false; }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,            $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT,        30);
        curl_setopt($curl, CURLOPT_MAXREDIRS,      7);
        curl_setopt($curl, CURLOPT_HEADER,         false);

        $data = curl_exec($curl);
        if($data === false) {
            $data = curl_error($curl);
        }
        curl_close($curl);

        return $data;
    }

    public function getCareer() {
        $data = $this->getData($this->career_url);
        return json_decode($data, true);
    }

    public function getHero($hero_id = null) {
        if($hero_id == null) { return false; }

        $data = $this->getData($this->hero_url.$hero_id);
        return json_decode($data, true);
    }

    public function getItem($item_data = null) {
        if($item_data == null) { return false; }

        $data = $this->getData($this->item_url.$item_data);
        return json_decode($data, true);
    }

    public function getFollower($follower_type = null) {
        if($follower_type == null || !in_array($follower_type, $this->followerTypes)) { return false; }

        $data = $this->getData($this->follower_url.$follower_type);
        return json_decode($data, true);
    }

    public function getArtisan($artisan_type = null) {
        if($artisan_type == null || !in_array($artisan_type, $this->artisanTypes)) { return false; }

        $data = $this->getData($this->artisan_url.$artisan_type);
        return json_decode($data, true);
    }

    public function __desctruct() {
        unset($this->battlenet_tag);
    }
}
