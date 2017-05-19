# !!! No longer working due to changes to the API !!!

# Diablo 3 API PHP
A Diablo 3 Web API wrapper written in PHP.

This is meant to be very simple, easy to use and modify.
It supports the 'If-Modified-Since' header.
o
Create an account at [dev.battle.net](https://dev.battle.net/) to obtain an API Key.

### Caching
If caching is enabled when calling the API, the 'If-Modified-Since' header will be effect. This however does not save on API points. Points will still be deducted from your limit. A '304 Not Modified' response from the API will only save bandwidth and time.

### Required Folder Structure (for images and cache):
* root
  * cache
  * img
     * items
         * large
         * small
      * skills
         * 21
         * 42
         * 64

### Methods Available
+ Get Career Profile
+ Get Hero Profile
+ Get Item Information
+ Get Follower Information
+ Get Artisan Information
+ Get Item Image
+ Get Skill Image
+ Get Skill Tooltip
+ Get Skill Rune Tooltip
+ Get All Hero Item Images
+ Get All Hero Skill Images
+ Get Paperdoll

Official Site: [https://dev.battle.net/](https://dev.battle.net/)

Original Documentation: [https://dev.battle.net/io-docs](https://dev.battle.net/io-docs)

_Dual Licensed: MIT/GPL_
