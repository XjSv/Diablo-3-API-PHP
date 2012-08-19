<?php
require_once('diablo3.api.class.php');

$Diablo3       = new Diablo3("XjSv#1677");                                                      // Battle.net Tag ID. 'XjSv#1677' or 'XjSv-1677' (string)
$CAREER_DATA   = $Diablo3->getCareer();
$HERO_DATA     = $Diablo3->getHero(3982160);                                                    // Hero ID (int)
$ITEM_DATA     = $Diablo3->getItem('COGHsoAIEgcIBBXIGEoRHYQRdRUdnWyzFB2qXu51MA04kwNAAFAKYJMD'); // Item Data 'COGHsoAIEgcIBBXIGEoRHYQRdRUdnWyzFB2qXu51MA04kwNAAFAKYJMD'  (string)
$FOLLOWER_DATA = $Diablo3->getFollower('templar');                                              // Options: 'enchantress', 'templar', 'scoundrel' (string)
$ARTISAN_DATA  = $Diablo3->getArtisan('blacksmith');                                            // Options: 'blacksmith', 'jeweler' (string)

print_r($CAREER_DATA);
print_r($HERO_DATA);
print_r($ITEM_DATA);
print_r($FOLLOWER_DATA);
print_r($ARTISAN_DATA);