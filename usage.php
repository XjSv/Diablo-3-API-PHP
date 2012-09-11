<?php
// For timing
$time  = microtime();
$time  = explode(' ', $time);
$time  = $time[1] + $time[0];
$start = $time;
// For timing

require_once('diablo3.api.class.php');

// Optional
//
require_once('functions.php');

// Settings
//
set_time_limit(0);
ini_set('memory_limit', '128M');

$Diablo3       = new Diablo3("XjSv#1677", 'us', 'en_US');                                       // Battle.net Tag. (e.g. 'XjSv#1677' or 'XjSv-1677') (string), Server: 'us', 'eu', etc. (string) [Optional, Defaults to 'us'], Locale: 'en_US', 'es_MX', etc. (string)
$CAREER_DATA   = $Diablo3->getCareer();
$HERO_DATA     = $Diablo3->getHero(3982160);                                                    // Hero ID (int)
$ITEM_DATA     = $Diablo3->getItem('COGHsoAIEgcIBBXIGEoRHYQRdRUdnWyzFB2qXu51MA04kwNAAFAKYJMD'); // Item Data 'COGHsoAIEgcIBBXIGEoRHYQRdRUdnWyzFB2qXu51MA04kwNAAFAKYJMD'  (string)
$FOLLOWER_DATA = $Diablo3->getFollower('templar');                                              // Options: 'enchantress', 'templar', 'scoundrel' (string)
$ARTISAN_DATA  = $Diablo3->getArtisan('blacksmith');                                            // Options: 'blacksmith', 'jeweler' (string)

// Before handling the data check to make sure the return is an array
//
if(is_array($CAREER_DATA)) {
    print_r($CAREER_DATA);
} else {
    echo $CAREER_DATA; // Error message
}

if(is_array($HERO_DATA)) {
    print_r($HERO_DATA);
} else {
    echo $HERO_DATA; // Error message
}

if(is_array($ITEM_DATA)) {
    print_r($ITEM_DATA);
} else {
    echo $ITEM_DATA; // Error message
}

if(is_array($FOLLOWER_DATA)) {
    print_r($FOLLOWER_DATA);
} else {
    echo $FOLLOWER_DATA; // Error message
}

if(is_array($ARTISAN_DATA)) {
    print_r($ARTISAN_DATA);
} else {
    echo $ARTISAN_DATA; // Error message
}

// Function included in functions.php
//
//$user_profiles = array("XjSv#1677", "ZijaD#1113", "taDo#1510");
//print_r(getAllTheData($user_profiles, 'us', 'en_US'));

// For timing
$time       = microtime();
$time       = explode(' ', $time);
$time       = $time[1] + $time[0];
$finish     = $time;
$total_time = round(($finish - $start), 4);
$total_time = secondsToTime($total_time);
echo '<br>Proccess finished in '.$total_time.' seconds.'."<br>";
// For timing
