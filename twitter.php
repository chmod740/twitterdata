<?php
    
    require_once('./TwitterAPIWrapper.php');
    
    $settings = array(
                      'oauth_access_token' => "1058206495-eDSbO00tXNxawv5HOSOnJ5X1bqYWbRACCZVmZTu",
                      'oauth_access_token_secret' => "MhUGlnFKBtyBCu1pal0yRdRlORwHsTBbEp53HSIDtM",
                      'consumer_key' => "3HiQdQdwnQsAkcDHxk23Rw",
                      'consumer_secret' => "ujY0ijb9hNex8OgxcCv3p8Eqpnoyhg0VirbiPr9I"
                      );
    
    
    $url = 'https://api.twitter.com/1.1/users/lookup.json';
    $getfield = '?user_id=783214,6483922,746843,6354349';
    
//    $url = 'https://api.twitter.com/1.1/users/search.json';
//    $getfield = '?q=a';
    
    $requestMethod = 'GET';
    
    $twitter = new TwitterAPIExchange($settings);
    echo $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();
    
    
?>
