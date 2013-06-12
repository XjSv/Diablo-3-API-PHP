# Diablo 3 API PHP
A Diablo 3 Web API wrapper written in PHP.  

This is meant to be very simple, easy to use and modify.  
It supports authenticated API calls and 'If-Modified-Since' header.  

### Caching
If caching is enabled when calling the API, the 'If-Modified-Since' header will be effect. This however does not save on API points. Points will still be deducted from your limit. A '304 Not Modified' response from the API will only save bandwidth and time. At the time of writing this, career and hero data do not honor 'If-Modified-Since' header and do not return a 'Last-Modified' header neither. Because of this, career and hero data are always received from the API not cache. 

### API Limits (as of 6/12/2013)
Unauthenticated API Limit: **30,000**  
Authenticated API Limit: **300,000**  

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

Original Blizzard API Documentation: [http://blizzard.github.com/d3-api-docs/](http://blizzard.github.com/d3-api-docs/)

_Dual Licensed: MIT/GPL_
