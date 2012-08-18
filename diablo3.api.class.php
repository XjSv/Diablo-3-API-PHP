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
            $hash = strpos($battlenet_tag, '#');
            if($hash !== false) {
                $battlenet_tag = str_replace('#', '-', $battlenet_tag);
            }

            //  Check if its a valid Battle.net tag (Pending)
            //
            /*if(!preg_match('/^.+(-[0-9]{4})/', $battlenet_tag)) {
                error_log("Battle.net tag provided not valid.");
                exit(0);
            }*/

            //  Set Variables
            //
            $this->battlenet_tag = (string)$battlenet_tag;
            $this->career_url    = $this->protocol.$this->host.'/api/d3/profile/'.$this->battlenet_tag.'/';
            $this->hero_url      = $this->protocol.$this->host.'/api/d3/profile/'.$this->battlenet_tag.'/hero/';
        } else {
            error_log("Required Battle.net tag");
            exit(0);
        }

        $this->item_url     = $this->protocol.$this->host.'/api/d3/data/item/';
        $this->follower_url = $this->protocol.$this->host.'/api/d3/data/follower/';
        $this->artisan_url  = $this->protocol.$this->host.'/api/d3/data/artisan/';
    }

    private function cURLcheckBasics() {
        if(!function_exists("curl_init") &&
           !function_exists("curl_setopt") &&
           !function_exists("curl_exec") &&
           !function_exists("curl_close")) return false;
        else return true;
    }

    private function getData($url) {
        if($url == '') return false;
        if(!$this->cURLcheckBasics()) {
            error_log("cURL is NOT Available");
            return false;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,            $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT,        30);
        curl_setopt($curl, CURLOPT_MAXREDIRS,      7);
        curl_setopt($curl, CURLOPT_HEADER,         false);

        $data = curl_exec($curl);
        curl_close($curl);

        // Debug
        //
        //error_log("URL: ".$url);
        //error_log("Data: ".$data);

        $error = strpos($data, 'Error report');
        if($error) {
            $data = false;
        } else {
            $data = json_decode($data, true);
        }

        if(isset($data['code']) && ($data['code'] == 'OOPS' || $data['code'] == 'LIMITED')) {
            error_log('API Fail Reason: '.$data['reason']);
            $data = false;
        }

        return $data;
    }

    public function getCareer() {
        $data = $this->getData($this->career_url);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    public function getHero($hero_id = null) {
        if($hero_id == null || !preg_match('/^[0-9]+$/', $hero_id)) return 'Invalid/Empty Hero Id';

        $data = $this->getData($this->hero_url.$hero_id);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    public function getItem($item_data = null) {
        if($item_data == null || !preg_match('/^[a-zA-Z0-9]+$/', $item_data)) return 'Invalid/Empty Item Data';

        $data = $this->getData($this->item_url.$item_data);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    public function getFollower($follower_type = null) {
        if($follower_type == null || !in_array($follower_type, $this->followerTypes)) return 'Invalid/Empty Follower Type';

        $data = $this->getData($this->follower_url.$follower_type);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    public function getArtisan($artisan_type = null) {
        if($artisan_type == null || !in_array($artisan_type, $this->artisanTypes)) return 'Invalid/Empty Artisan Type';

        $data = $this->getData($this->artisan_url.$artisan_type);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    public function __desctruct() {
        unset($this->battlenet_tag);
    }
}
