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

    private function __construct($battlenet_tag) {
        $this->battlenet_tag = $battlenet_tag;
        $this->career_url    = $this->protocol.$this->host.'/api/d3/account/'.$this->battlenet_tag;
        $this->hero_url      = $this->protocol.$this->host.'/api/d3/account/'.$this->battlenet_tag.'/hero/';
        $this->item_url      = $this->protocol.$this->host.'/api/d3/data/item/';
        $this->follower_url  = $this->protocol.$this->host.'/api/d3/data/follower/';
        $this->artisan_url   = $this->protocol.$this->host.'/api/d3/data/artisan/';
    }

    private function getData($url) {
        if($url) { return false; }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,            $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT,        30);
        curl_setopt($curl, CURLOPT_MAXREDIRS,      7);

        $data = curl_exec($curl);
        if($data === false) {
            $data = curl_error($curl);
        }
        curl_close($curl);

        return $data;
    }

    public function getCareer() {
        $data = $this->getData($this->career_url);
        return $data;
    }

    public function getHero($hero_id = null) {
        if($hero_id == null) { return false; }

        $data = $this->getData($this->hero_url.$hero_id);
        return $data;
    }

    public function getItem($item_data = null) {
        if($item_data == null) { return false; }

        $data = $this->getData($this->item_url.$item_data);
        return $data;
    }

    public function getFollower($follower_type = null) {
        if($follower_type == null || !in_array($follower_type, $followerTypes)) { return false; }

        $data = $this->getData($this->follower_url.$follower_type);
        return $data;
    }

    public function getArtisan($artisan_type = null) {
        if($artisan_type == null || !in_array($artisan_type, $artisanTypes)) { return false; }

        $data = $this->getData($this->artisan_url.$artisan_type);
        return $data;
    }

    private function __desctruct() {
        unset($this->battlenet_tag);
    }
}

$Diablo3 = new Diablo3("XjSv-1677");

$CAREER_DATA   = $Diablo3->getCareer();
$HERO_DATA     = $Diablo3->getHero(1);
$ITEM_DATA     = $Diablo3->getItem('COGHsoAIEgcIBBXIGEoRHYQRdRUdnWyzFB2qXu51MA04kwNAAFAKYJMD');
$FOLLOWER_DATA = $Diablo3->getFollower('enchantress');
$ARTISAN_DATA  = $Diablo3->getArtisan('blacksmith');