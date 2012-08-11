<?php
require_once('diablo3.api.class.php');

$Diablo3 = new Diablo3("XjSv-1677");

$CAREER_DATA   = $Diablo3->getCareer();
//$HERO_DATA     = $Diablo3->getHero(3982160);
//$ITEM_DATA     = $Diablo3->getItem('COGHsoAIEgcIBBXIGEoRHYQRdRUdnWyzFB2qXu51MA04kwNAAFAKYJMD');
//$FOLLOWER_DATA = $Diablo3->getFollower('enchantress');
//$ARTISAN_DATA  = $Diablo3->getArtisan('blacksmith');

var_dump($CAREER_DATA);