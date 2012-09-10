<?php
class Diablo3 {
    private $battlenet_tag;
    private $protocol          = 'http://';
    private $host              = '.battle.net';
    private $battlenet_servers = array('us', 'eu', 'tw', 'kr', 'cn');
    private $locales           = array('en_US', 'es_MX', 'en_GB', 'it_IT', 'es_ES', 'pt_PT', 'fr_FR', 'ru_RU', 'pl_PL', 'de_DE', 'ko_KR', 'en_US', 'zh_TW', 'en_US', 'zh_CN', 'en_US');
    private $followerTypes     = array('enchantress', 'templar', 'scoundrel');
    private $artisanTypes      = array('blacksmith', 'jeweler');
    private $blizzardErrors    = array('OOPS', 'LIMITED', 'MAINTENANCE', 'NOTFOUND');
    private $current_locale;
    private $career_url;
    private $hero_url;
    private $item_url;
    private $follower_url;
    private $artisan_url;

    public function __construct($battlenet_tag, $server = 'us', $locale = 'en_US') {
        if($battlenet_tag !== '') {
            $hash = strpos($battlenet_tag, '#');
            if($hash !== false) {
                $battlenet_tag = str_replace('#', '-', $battlenet_tag);
            }

            if(!in_array($server, $this->battlenet_servers, true)) {
                $server = 'us';
            } else if($server == 'cn') {
                $server     = '';
                $this->host = 'www.battlenet.com.cn'; // Hack for 'cn.battle.net'
            }

            if(!in_array($locale, $this->locales, true)) {
                $locale = 'en_US';
            }

            //  Check if its a valid Battle.net tag (Pending)
            //
            /*if(!preg_match('/^.+(-[0-9]{4})/', $battlenet_tag)) {
                error_log("Battle.net tag provided not valid.");
                exit(0);
            }*/

            //  Set Variables
            //
            $this->current_locale = $locale;
            $this->battlenet_tag  = urlencode($battlenet_tag);
            $this->career_url     = $this->protocol.$server.$this->host.'/api/d3/profile/'.$this->battlenet_tag.'/index';
            $this->hero_url       = $this->protocol.$server.$this->host.'/api/d3/profile/'.$this->battlenet_tag.'/hero/';
        } else {
            error_log("Required Battle.net tag");
            exit(0);
        }

        $this->item_url     = $this->protocol.$server.$this->host.'/api/d3/data/item/';
        $this->follower_url = $this->protocol.$server.$this->host.'/api/d3/data/follower/';
        $this->artisan_url  = $this->protocol.$server.$this->host.'/api/d3/data/artisan/';
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

        // Append Locale Variable
        //
        $url = $url.'?locale='.$this->current_locale;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,            $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT,        20);
        curl_setopt($curl, CURLOPT_MAXREDIRS,      3);
        curl_setopt($curl, CURLOPT_HEADER,         false);
        curl_setopt($curl, CURLOPT_PROTOCOLS,      CURLPROTO_HTTP);

        $data  = curl_exec($curl);
        $error = curl_errno($curl);

        if($error) {
            error_log('cURL Error: '.$error);
            $data = false;
        } else {
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // Debug
            //
            //error_log("URL: ".$url);
            //error_log("HTTP Code: : ".$http_status);
            //error_log("Data: ".$data);

            if($http_status == 503) {
                $data = false;
            } else if($http_status == 404) {
                $data = false;
            } else if($http_status == 200) {
                $data = json_decode($data, true);
            }

            if(isset($data['code']) && (in_array($data['code'], $this->blizzardErrors, true))) {
                error_log('API Fail Reason: '.$data['reason']);
                $data = false; // In case http status is other then 503 or 404
            }
        }

        curl_close($curl);

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
        if($item_data == null || !preg_match('/^[a-zA-Z0-9_-]+$/', $item_data)) return 'Invalid/Empty Item Data';

        $data = $this->getData($this->item_url.$item_data);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    public function getFollower($follower_type = null) {
        if($follower_type == null || !in_array($follower_type, $this->followerTypes, true)) return 'Invalid/Empty Follower Type';

        $data = $this->getData($this->follower_url.$follower_type);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    public function getArtisan($artisan_type = null) {
        if($artisan_type == null || !in_array($artisan_type, $this->artisanTypes, true)) return 'Invalid/Empty Artisan Type';

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
