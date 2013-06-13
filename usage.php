<?php
// For Timing
$time  = microtime();
$time  = explode(' ', $time);
$time  = $time[1] + $time[0];
$start = $time;

require_once('diablo3.api.class.php');

// PHP Settings
set_time_limit(0);
error_reporting(E_ALL);
ini_set('memory_limit',  '256M');
ini_set('display_errors', true);

/***************************************************************************************/

// Instantiate The Diablo3 Object. Parameters are optional.
// These are some examples ranging from no parameters to all parameters.
//
// Parameter Order: (BattleTag, Server, Locale)
// Brakedown - BattleTag: 'XjSv#1677' or 'XjSv-1677' (string)
//             Server:    'us', 'eu' etc. (string)
//             Locale:    'en_US', 'pt_BR', 'es_MX', etc. (string)
//
// $Diablo3 = new Diablo3();
// $Diablo3 = new Diablo3("XjSv#1677");
// $Diablo3 = new Diablo3('', 'us');
// $Diablo3 = new Diablo3('', '', 'en_US');
// $Diablo3 = new Diablo3("XjSv#1677", 'us', 'en_US');
//
$Diablo3 = new Diablo3("XjSv#1677");

// Call Available Methods To Return Data.
// In this case since we did not provide a BattleTag we will get
// an error message: 'Function not available without a BattleTag'
//
echo "Career Data:";
$CAREER_DATA = $Diablo3->getCareer();

// Before handling the data check to make sure the return is an array
// If the data is not an array then something wen't wrong.
//
if(is_array($CAREER_DATA)) {
    echo '<pre>';
    var_dump($CAREER_DATA);
    echo '</pre>';
}

// Get Hero Data By ID
//
echo "<br>Hero Data:";
$HERO_DATA = $Diablo3->getHero(3982160);
if(is_array($HERO_DATA)) {
    echo '<pre>';
    var_dump($HERO_DATA);
    echo '</pre>';
}

// Get Item Data
//
echo "<br>Item Data:";
$ITEM_DATA = $Diablo3->getItem('item/COGHsoAIEgcIBBXIGEoRHYQRdRUdnWyzFB2qXu51MA04kwNAAFAKYJMD');

// If caching is enabled you could check if the data came from cache or the API.
// This reflects the last call made to the API.
//
if($Diablo3->resultsFromCache()) {
    echo "<br>This data is returned from cache.";
} else {
    echo "<br>This data is returned from the API.";
}

if(is_array($ITEM_DATA)) {
    echo '<pre>';
    var_dump($ITEM_DATA);
    echo '</pre>';
}

// Get Item Information Data
//
echo "<br>Item Info Data:";
$ITEM_INFO_DATA = $Diablo3->getItemById('Unique_Helm_006_104');
if(is_array($ITEM_INFO_DATA)) {
    echo '<pre>';
    var_dump($ITEM_INFO_DATA);
    echo '</pre>';
}

// Get Follower Data
// Your options are: 'enchantress', 'templar', 'scoundrel'
//
echo "<br>Follower Data:";
$FOLLOWER_DATA = $Diablo3->getFollower('templar');
if(is_array($FOLLOWER_DATA)) {
    echo '<pre>';
    var_dump($FOLLOWER_DATA);
    echo '</pre>';
}

// Get Artisan Data
// Your options are: 'blacksmith', 'jeweler'
//
echo "<br>Artisan Data:";
$ARTISAN_DATA = $Diablo3->getArtisan('blacksmith');
if(is_array($ARTISAN_DATA)) {
    echo '<pre>';
    var_dump($ARTISAN_DATA);
    echo '</pre>';
}

/***************************************************************************************/

/***************** Images & ToolTips (not part of the official API) ********************/

// Get Item Image
// Parameter Order: (Icon Name, Size)
// Brakedown - Name: 'unique_chest_013_104_demonhunter_male', etc.
//             Size: 'small' or 'large'
// Returns the location of the image or false on failure.
//
echo "<br>Item Image: <br>";
$ITEM_IMAGE = $Diablo3->getItemImage('unique_chest_013_104_demonhunter_male', 'large');
if(!empty($ITEM_IMAGE)) {
    echo '<img src="'.$ITEM_IMAGE.'">';
}

// Get Skill Image
// Parameter Order: (Icon Name, Size)
// Brakedown - Name: 'barbarian_frenzy', etc.
//             Size: '21', '42' or '64'
// Returns the location of the image or false on failure.
//
echo "<br>Skill Image: <br>";
$SKILL_IMAGE = $Diablo3->getSkillImage('barbarian_frenzy', '64');
if(!empty($SKILL_IMAGE)) {
    echo '<img src="'.$SKILL_IMAGE.'">';
}

// Get PaperDoll Image
// Parameter Order: (Class, Gender)
// Brakedown - Class: 'barbarian', 'witch-doctor', 'demon-hunter', 'monk' or 'wizard'
//             Gender: 'male' or 'female'
// Returns the location of the image or false on failure.
//
echo "<br>Paperdoll: <br>";
$PAPERDOLL = $Diablo3->getPaperDoll('barbarian', 'female');
if(!empty($PAPERDOLL)) {
    echo '<img src="'.$PAPERDOLL.'">';
}

// Get All Item Images for 1 Hero
// Parameter Order: (Hero ID, Size)
// Brakedown - Name: 'unique_chest_013_104_demonhunter_male', etc.
//             Size: 'small' or 'large'
// Returns true on success.
//
$allItemImages = $Diablo3->getAllHeroItemImages(3982160, 'small');
if($allItemImages) {
    echo "<br>All Hero Item Images Saved";
}

// Get All Skill Image for 1 Hero
// Parameter Order: (Hero ID, Size)
// Brakedown - Hero ID: 3982160
//             Size: 21, 42 or 64
// Returns true on success.
//
$allSkillImages = $Diablo3->getAllHeroSkillImages(3982160, 42);
if($allSkillImages) {
    echo "<br>All Hero Skill Images Saved";
}

// Get ToolTip Skill Data (for javascript handling)
// Parameter Order: (tooltipUrl, boolean)
// Brakedown - tooltipUrl: 'skill/barbarian/frenzy', etc.
//             Boolean: true for jsonp, false or leave empty for json.
//
echo "<br>Skill Tooltip: <br>";
$SKILL_TOOLTIP = $Diablo3->getSkillToolTip('skill/barbarian/frenzy', true);
if(!empty($SKILL_TOOLTIP)) {
    echo $SKILL_TOOLTIP;
}

// Get ToolTip Rune Data (for javascript handling)
// Parameter Order: (tooltipUrl, boolean)
// Brakedown - tooltipUrl: 'rune/frenzy/a', etc.
//             Boolean: true for jsonp, false or leave empty for json.
//
echo "<br>Skill Rune Tooltip: <br>";
$SKILL_RUNE_TOOLTIP = $Diablo3->getSkillToolTip('rune/frenzy/a');
if(!empty($SKILL_RUNE_TOOLTIP)) {
    echo $SKILL_RUNE_TOOLTIP;
}

/***************************************************************************************/

// For Timing
$time         = microtime();
$time         = explode(' ', $time);
$time         = $time[1] + $time[0];
$finish       = $time;
$total_time   = round(($finish - $start), 4);
$total_memory = round((memory_get_usage() / 1048576), 2); // Calculate Memory Usage Based on MB

echo "<br>Proccess finished in {$total_time} seconds<br>Memory Used: {$total_memory} MB";
