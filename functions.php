<?php
function secondsToTime($seconds) {
    // extract hours
    $hours = floor($seconds / (60 * 60));

    // extract minutes
    $divisor_for_minutes = $seconds % (60 * 60);
    $minutes             = floor($divisor_for_minutes / 60);

    // extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $seconds             = ceil($divisor_for_seconds);

    // return the final array
    $obj = array("h" => (int)$hours,
                 "m" => (int)$minutes,
                 "s" => (int)$seconds);

    $time = implode(':', $obj);

    return $time;
}

function getAllTheData($battlenet_tags = array(), $server = 'us', $locale = 'en') {
    if(!(count($battlenet_tags) > 0)) return false;
    if(!in_array($server, array('us', 'eu', 'sea', 'tw', 'kr'), true)) $server = 'us';
    if(!in_array($locale, array('en', 'es', 'pt', 'it', 'de', 'fr', 'pl', 'ru', 'tr', 'ko', 'zh'), true)) $locale = 'en';

    $return_array = array();

    require_once('diablo3.api.class.php');

    foreach($battlenet_tags as $user) {
        $Diablo3     = new Diablo3($user, $server, $locale);  // Battle.net Tag ID. 'XjSv#1677' or 'XjSv-1677' (string), Server (string) "en"
        $CAREER_DATA = $Diablo3->getCareer();

        if(is_array($CAREER_DATA)) {
            $return_array['CAREER_DATA'] = $CAREER_DATA;

            $hero_list = array();
            foreach($CAREER_DATA['heroes'] as $key => $value) {
                $hero_list[] = $value['id'];
            }

            $HERO_DATA = array();
            foreach($hero_list as $key => $hero) {
                $HERO_DATA[] = $Diablo3->getHero($hero); // Hero ID (int)
            }

            $return_array['HERO_DATA'] = $HERO_DATA;

            //$FOLLOWER_DATA = $Diablo3->getFollower('templar');   // Options: 'enchantress', 'templar', 'scoundrel' (string)
            //$ARTISAN_DATA  = $Diablo3->getArtisan('blacksmith'); // Options: 'blacksmith', 'jeweler' (string)

            // Hero
            //
            foreach($HERO_DATA as $key => $hero) {
                if(is_array($hero)) {
                    // Items
                    //
                    $hero_items = array();
                    foreach($hero['items'] as $key3 => $value3) {
                        $hero_items[$hero['id']][$key3] = $value3['tooltipParams'];
                    }

                    foreach($hero_items as $hero_id => $items) {
                        foreach($items as $name => $tooltipParams) {
                            $tooltipParams = str_replace('item/', '', $tooltipParams);
                            $ITEM_DATA[$hero_id][$name] = $Diablo3->getItem($tooltipParams); // Item Data 'COGHsoAIEgcIBBXIGEoRHYQRdRUdnWyzFB2qXu51MA04kwNAAFAKYJMD'  (string)
                        }
                    }

                    foreach($ITEM_DATA[$hero['id']] as $key4 => $value4) {
                        if(is_array($value4)) {
                            $value4['_itemType']       = $key4;
                            $return_array['ITEM_DATA'] = $value4;
                        } else {
                            $return_array['ITEM_DATA'] = $value4;
                        }
                    }
                } else {
                    $return_array['HERO_DATA'] = $hero;
                }
            }
        } else {
            $return_array['CAREER_DATA'] = $CAREER_DATA;
        }
    }

    return $return_array;
}
