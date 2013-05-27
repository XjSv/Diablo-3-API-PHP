<?php
/**
* Diablo 3 Web API PHP Bridge
*
* This is meant to be very simple & easy to use.
* You are free to repurpose this for any other coding needs or projects.
*
*
* @link http://www.armandotresova.com/projects/diablo_3_api_php.html
*/

class Diablo3 {
    private $battlenet_tag;
    private $host                = '.battle.net';
    private $media_host          = '.media.blizzard.com';
    private $battlenet_servers   = array('us', 'eu', 'tw', 'kr', 'cn');
    private $locales             = array('en_US', 'en_GB', 'es_MX', 'es_ES', 'it_IT', 'pt_PT', 'pt_BR', 'fr_FR', 'ru_RU', 'pl_PL', 'de_DE', 'ko_KR', 'zh_TW', 'zh_CN');
    private $followerTypes       = array('enchantress', 'templar', 'scoundrel');
    private $artisanTypes        = array('blacksmith', 'jeweler');
    private $blizzardErrors      = array('OOPS', 'LIMITED', 'MAINTENANCE', 'NOTFOUND');
    private $current_locale;
    private $current_server;
    private $career_url;
    private $hero_url;
    private $item_url;
    private $follower_url;
    private $artisan_url;
    private $item_img_url;
    private $item_img_sizes      = array('small', 'large');
    private $skill_img_url;
    private $skill_img_sizes     = array('21', '42', '64');
    private $item_save_loc       = '/Diablo-3-API-PHP/img/items/';       // Relative to DOCUMENT_ROOT
    private $skills_save_loc     = '/Diablo-3-API-PHP/img/skills/';      // Relative to DOCUMENT_ROOT
    private $paperdolls_save_loc = '/Diablo-3-API-PHP/img/paperdolls/';  // Relative to DOCUMENT_ROOT
    private $skill_url;
    private $paperdoll_url;
    private $genders             = array('male', 'female');
    private $classes             = array('barbarian', 'witch-doctor', 'demon-hunter', 'monk', 'wizard');
    private $authenticate        = false;       // Set to true for authenticated calls
    private $API_private_key     = '';          // API Private Key
    private $API_public_key      = '';          // API Public Key
    private $last_time_accessed  = 0;

    public function __construct($battlenet_tag, $server = 'us', $locale = 'en_US') {
        if(!empty($battlenet_tag)) {
            $hash = strpos($battlenet_tag, '#');
            if($hash !== false) {
                $battlenet_tag = str_replace('#', '-', $battlenet_tag);
            }

            if(!in_array($server, $this->battlenet_servers, true)) {
                $server = 'us';
            } else if($server == 'cn') {
                $server = '';
                $this->host = 'www.battlenet.com.cn'; // 'cn.battle.net'
                $this->media_host = 'content.battlenet.com.cn'; // 'cn.media.blizzard.com'
            }

            if(!in_array($locale, $this->locales, true)) {
                $locale = 'en_US';
            }

            // Check if its a valid Battle.net tag
            //
            if(!$this->checkBattletag($battlenet_tag)) {
                error_log("Battle.net tag provided not valid. ({$battlenet_tag})");
                exit(0);
            }

            // Set Variables
            //
            $this->current_locale = $locale;
            $this->current_server = $server;
            $this->battlenet_tag  = urlencode($battlenet_tag);
            $this->career_url     = 'http://'.$server.$this->host.'/api/d3/profile/'.$this->battlenet_tag.'/index';
            $this->hero_url       = 'http://'.$server.$this->host.'/api/d3/profile/'.$this->battlenet_tag.'/hero/';
        } else {
            error_log("Required Battle.net tag");
            exit(0);
        }

        // TODO: Remove Battle.net Tag dependency if you want to just use this part of the API
        //
        $this->item_url      = 'http://'.$server.$this->host.'/api/d3/data/';
        $this->follower_url  = 'http://'.$server.$this->host.'/api/d3/data/follower/';
        $this->artisan_url   = 'http://'.$server.$this->host.'/api/d3/data/artisan/';
        $this->item_img_url  = 'http://'.$server.$this->media_host.'/d3/icons/items/';
        $this->skill_img_url = 'http://'.$server.$this->media_host.'/d3/icons/skills/';
        $this->skill_url     = 'http://'.$server.$this->host.'/d3/'.substr($locale, 0, -3).'/tooltip/';
        $this->paperdoll_url = 'http://'.$server.$this->host.'/d3/static/images/profile/hero/paperdoll/';
    }

    /**
     * checkBattletag
     * Checks if the battle tag meets the requirements
     * https://us.battle.net/support/en/article/battletag-naming-policy
     *
     * @param  string $battlenet_tag [description]
     * @return boolean               [description]
     */
    public function checkBattletag($battlenet_tag) {
        $pattern = '/^[\p{L}\p{Mn}][\p{L}\p{Mn}0-9]{2,11}-[0-9]{4,5}+$/u';
        return (preg_match($pattern, $battlenet_tag)) ? true : false;
        return true;
    }

    /**
     * cURLcheckBasics
     * Checks to see if required cURL functions are available
     *
     * Parameters:
     *     (name) - about this param
     */
    private function cURLcheckBasics() {
        if(!function_exists("curl_init")   &&
           !function_exists("curl_setopt") &&
           !function_exists("curl_exec")   &&
           !function_exists("curl_close")) return false;
        else return true;
    }

    /**
     * curlSaveImage
     * Get image with cURL, save it in $item_save_loc and return the image location
     *
     * Parameters:
     *     (location) - save location (items or skills)
     *     (url)      - a valid URL
     *     (icon)     - icon name
     *     (size)     - image sizes
     */
    private function curlSaveImage($location, $url, $icon, $size = '') {
        if(empty($location) || empty($url) || empty($icon)) return false;

        switch($location) {
            case 'items':
                $real_item_path  = $_SERVER['DOCUMENT_ROOT'].$this->item_save_loc;
                $return_location = $this->item_save_loc;
                $size            = $size.'/';
                $ext             = '.png';
                break;
            case 'skills':
                $real_item_path  = $_SERVER['DOCUMENT_ROOT'].$this->skills_save_loc;
                $return_location = $this->skills_save_loc;
                $size            = $size.'/';
                $ext             = '.png';
                break;
            case 'paperdolls':
                $real_item_path  = $_SERVER['DOCUMENT_ROOT'].$this->paperdolls_save_loc;
                $return_location = $this->paperdolls_save_loc;
                $size            = '';
                $ext             = '.jpg';
                break;
            default:
                return false;
        }

        if(!file_exists($real_item_path.$size.$icon.$ext)) {
            if(is_dir($real_item_path.$size) && is_writable($real_item_path.$size)) {
                if(!$this->cURLcheckBasics()) {
                    error_log("cURL is NOT Available");
                    return false;
                }

                $fp   = fopen($real_item_path.$size.$icon.$ext, 'wb');
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL,            $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
                curl_setopt($curl, CURLOPT_FILE,           $fp);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($curl, CURLOPT_TIMEOUT,        20);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_MAXREDIRS,      5);
                curl_setopt($curl, CURLOPT_HEADER,         false);
                curl_setopt($curl, CURLOPT_PROTOCOLS,      CURLPROTO_HTTP);

                curl_exec($curl);
                $error = curl_errno($curl);

                if($error) {
                    error_log('cURL Error: '.$error);
                    $data = false;
                } else {
                    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                    if($http_status >= 400 && $http_status <= 599) {
                        $data = false;
                    } else if($http_status >= 200 && $http_status <= 399) {
                        $data = $return_location.$size.$icon.$ext;
                    } else {
                        $data = false;
                    }
                }

                curl_close($curl);
                fclose($fp);

                return $data;
            } else {
                error_log("Wrong Image Size or Directory: '".$real_item_path.$size."' not writable");
                return false;
            }
        } else {
            return $return_location.$size.$icon.$ext;
        }
    }

    /**
     * curlRequest
     * Basic cURL request
     *
     * Parameters:
     *     (url) - a valid URL
     */
    private function curlRequest($url) {
        if($url == '') return false;
        if(!$this->cURLcheckBasics()) {
            error_log("cURL is NOT Available");
            return false;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,            $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($curl, CURLOPT_TIMEOUT,        10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS,      5);
        curl_setopt($curl, CURLOPT_HEADER,         false);
        curl_setopt($curl, CURLOPT_PROTOCOLS,      CURLPROTO_HTTP);

        // Check last accessed time
        //
        if($this->last_time_accessed > 0) {
            $last_time_accessed = $this->last_time_accessed / 1000;
            curl_setopt($ch, CURLOPT_TIMECONDITION, CURL_TIMECOND_IFMODSINCE);
            curl_setopt($ch, CURLOPT_TIMEVALUE, $last_time_accessed);
        }

        // Authenticate with Battle.net
        //
        if($this->authenticate) {
            date_default_timezone_set('GMT');
            $request_url = str_replace('http://'.$this->current_server.$this->host, '', $url);
            $date        = date('D, d M Y G:i:s T', time());
            $signature   = base64_encode(hash_hmac('sha1', "GET\n".$date."\n".$request_url."\n", $this->API_private_key, true));

            $header = array("Host: ".$this->current_server.$this->host,
                            "Date: ". $date,
                            "\nAuthorization: BNET ".$this->API_public_key.":".$signature."\n");

            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        $data       = curl_exec($curl);
        $error_no   = curl_errno($curl);
        $curl_error = curl_error($curl);

        if($error_no) {
            error_log('cURL Error: '.$error_no.' ('.$curl_error.') URL: '.$url);
            $data = false;
        } else {
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if($http_status >= 400 && $http_status <= 599) {
                error_log('Error Data Return: '.$data);
                $data = false;
            } else if($http_status >= 200 && $http_status <= 399) {
                // HTTP status good
            } else {
                error_log('Error Data Return: '.$data);
                $data = false;
            }
        }

        curl_close($curl);

        return $data;
    }

    /**
     * setLastTimeAccessed
     * Checks to see if required cURL functions are available
     *
     * Parameters:
     *     (date) - date time
     */
    public function setLastTimeAccessed($date = 0) {
        $this->last_time_accessed = $date;

        return true;
    }

    /**
     * getJsonData
     * Checks to see if required cURL functions are available
     *
     * Parameters:
     *     (name) - about this param
     */
    private function getJsonData($url) {
        if($url == '') return false;

        $data = $this->curlRequest($url);

        if($data) $data = json_decode($data, true);

        if(isset($data['code']) && (in_array($data['code'], $this->blizzardErrors, true))) {
            error_log('API Fail Reason: '.$data['reason'].' URL: '.$url);
            $data = false;
        }

        return $data;
    }
    
    /**
     * getAllItemImages
     * Gets all the item images from a hero ID. If no size is passed both will be processed
     * 
     * @param  int    $heroId [description]
     * @param  string $size    [description]
     * 
     */
    public function getAllItemImages($heroId, $size = '') {
        if(empty($heroId)) return 'Hero ID Empty';
        
        $hero_data = $this->getHero($heroeID);
        if(is_array($hero_data)) {
            foreach($hero_data['items'] as $key) {
                if(empty($size)) {
                    $item_image_small = $this->getItemImage($key['icon'], 'small');
                    $item_image_large = $this->getItemImage($key['icon'], 'large');
                } else {
                    $item_image = $this->getItemImage($key['icon'], $size);
                }   
            }
        } else {
            return 'No Data Return';
        }
    }

    /**
     * getItemImage
     * Item image
     *
     * Parameters:
     *     (icon)      - String of item icon name
     *     (imageSize) - Size of image (small or large)
     */
    public function getItemImage($icon = null, $imageSize = 'small') {
        if($icon == null || !in_array($imageSize, $this->item_img_sizes, true)) return 'Icon Name Empty or Invalid Size';

        $data = $this->curlSaveImage('items', $this->item_img_url.$imageSize.'/'.$icon.'.png', $icon, $imageSize);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

/**
     * getAllSkillImages
     * Get all the skill images from a heroe ID
     * 
     * @param  int    $heroId  The heroe id
     * @param  string $size    The size : 64, 42, 21
     * @return string          Error message if no valid size is sent
     * 
     */
    public function getAllSkillImages($heroId, $size = '') {
        if(empty($heroId)) return 'Hero ID Empty';
        
        $hero_data = $this->getHero($heroId);

        if(is_array($hero_data)) {
            foreach($hero_data['skills']['active'] as $skills) {
                if(isset($skills['skill']['icon'])) {
                    $skillname = $skills['skill']['icon'];
                    
                    // Checking the size
                    switch($size) {
                        case '64':
                            $this->getSkillImage($skillname, '64');
                            break;
                        case '41':
                            $this->getSkillImage($skillname, '42');
                            break;
                        case '21':
                            $this->getSkillImage($skillname, '21');
                            break;
                        case '':
                            $this->getSkillImage($skillname, '64');
                            $this->getSkillImage($skillname, '42');
                            $this->getSkillImage($skillname, '21');
                            break;
                        default:
                            error_log("Not a correct image size. Choose between 64, 42 or 21.");
                            return "Not a correct image size. Choose between 64, 42 or 21.";
                            break;
                    }
                }
            }
        } else {
            return 'No Data Return';
        }
    }
    
    /**
     * getSkillImage
     * Gets skill image
     *
     * Parameters:
     *     (icon)      - String of skill icon name
     *     (imageSize) - Size of image (21, 42 or 64)
     */
    public function getSkillImage($icon = null, $imageSize = '21') {
        if($icon == null || !in_array($imageSize, $this->skill_img_sizes, true)) return 'Icon Name Empty or Invalid Size';

        $data = $this->curlSaveImage('skills', $this->skill_img_url.$imageSize.'/'.$icon.'.png', $icon, $imageSize);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    /**
     * getSkillToolTip
     * Get Skill or Skill Rune Tooltip
     *
     * Parameters:
     *     (tooltipUrl) - String of tooltipUrl (e.g. rune/barbarian/frenzy/a)
     *     (jsonp)      - True to return in jsonp format (boolean)
     */
    public function getSkillToolTip($tooltipUrl = null, $jsonp = false) {
        if($tooltipUrl == null) return 'Tooltip Url Empty';

        $jsonp_ext = '';
        if($jsonp) {
            $jsonp_ext = '?format=jsonp';
        }

        $data = $this->curlRequest($this->skill_url.$tooltipUrl.$jsonp_ext);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    /**
     * getPaperDoll
     * Get character paperdoll (background image)
     *
     * Parameters:
     *     (class)  - Class (barbarian, witch-doctor, demon-hunter, monk, wizard)
     *     (gender) - Gender (male or female)
     */
    public function getPaperDoll($class = null, $gender = 'male') {
        if($class == null || !in_array($class, $this->classes, true) || !in_array($gender, $this->genders, true)) return 'No/Wrong class provided or wrong gender type.';

        $data = $this->curlSaveImage('paperdolls', $this->paperdoll_url.$class.'-'.$gender.'.jpg', $class.'-'.$gender);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    /**
     * getCareer
     * Gets career data
     *
     */
    public function getCareer() {
        $data = $this->getJsonData($this->career_url.'?locale='.$this->current_locale);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    /**
     * getHero
     * Gets hero data
     *
     * Parameters:
     *     (hero_id) - Hero ID (integer)
     */
    public function getHero($hero_id = null) {
        if($hero_id == null || !preg_match('/^[0-9]+$/', $hero_id)) return 'Invalid/Empty Hero Id';

        $data = $this->getJsonData($this->hero_url.$hero_id.'?locale='.$this->current_locale);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    /**
     * getItem
     * Gets item data
     *
     * Parameters:
     *     (item_data) - String of item data (e.g. 'item/COGHsoAIEgcIBBXIGEoRHYQRdRUdnWyzFB2qXu51MA04kwNAAFAKYJMD')
     */
    public function getItem($item_data = null) {
        if($item_data == null) return 'Empty Item Data';

        $data = $this->getJsonData($this->item_url.$item_data.'?locale='.$this->current_locale);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }
    
    /**
     * getItemById
     * Gets item data by ID
     *
     * Parameters:
     *     (item_id) - String of item id (e.g. 'Unique_Helm_006_104')
     */
    public function getItemById($item_id = null) {
        if($item_id == null) return 'Empty Item ID';

        $data = $this->getJsonData($this->item_url.'item/'.$item_id.'?locale='.$this->current_locale);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    /**
     * getFollower
     * Gets follower data
     *
     * Parameters:
     *     (follower_type) - String of the type of follower. Options available: 'enchantress', 'templar' & 'scoundrel'
     */
    public function getFollower($follower_type = null) {
        if($follower_type == null || !in_array($follower_type, $this->followerTypes, true)) return 'Invalid/Empty Follower Type';

        $data = $this->getJsonData($this->follower_url.$follower_type.'?locale='.$this->current_locale);

        if($data) {
            return $data;
        } else {
            return 'No Data Return';
        }
    }

    /**
     * getArtisan
     * Gets artisan data
     *
     * Parameters:
     *     (artisan_type) - String of the type of artisan. Options available: 'blacksmith' & 'jeweler'
     */
    public function getArtisan($artisan_type = null) {
        if($artisan_type == null || !in_array($artisan_type, $this->artisanTypes, true)) return 'Invalid/Empty Artisan Type';

        $data = $this->getJsonData($this->artisan_url.$artisan_type.'?locale='.$this->current_locale);

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
