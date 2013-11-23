<?php

require_once('./TwitterAPIWrapper.php');
// You will need to create a tokens.php file locally with the following lines in it
//
//	 $settings = array(
//                      'oauth_access_token' => "YOURTOKEN",
//                      'oauth_access_token_secret' => "YOURSECRET",
//                      'consumer_key' => "YOURKEY",
//                      'consumer_secret' => "YOUROTHERSECRET"
//                      );
//                      
// DO NOT ADD YOUR tokens.php FILE TO GIT VERSION CONTROL!!
require_once('./tokens.php');

// Get cmd args
if (empty($argv[1]) || empty($argv[2])) {
	die("\nERROR: arg 1 should be the name of the file to save data to. "
		               . "arg 2 should be one of [a, w] for append or overwrite\n\n"
					   . "Example: php getData.php data.json w\n\n");
}
$jsonFilename = $argv[1];
$action = $argv[2];

// Make twitter api object
$twitter = new TwitterAPIExchange($settings);

// Get seed users by letter
$numSeedUsers = 26;
$seedUsers = getSeedUsers($twitter, $numSeedUsers);

// Get seed users followers
$allUsers = array();
$depthlimit=3;
foreach($seedUsers as $user){
	// TODO: Maybe add follower_of field to each follower?
	array_merge($allUsers, getUsersFollowers($twitter, $user, $depthlimit));	
}
		
// add attributes
foreach($allUsers as $user) {
    parseDateAndUrl($user);
    addDateAttributesToUser($user);
}
// Save data as json file
$fp = fopen($jsonFilename, $action);
fwrite($fp, json_encode($allUsers));
fclose($fp);


// ******************************
// Function definitions
// ******************************


function getUsersFollowers($twitter, $user, $depthlimit=null){
	$users = array($user);
	
//	echo 'Getting followers for '.$user->id;
	
	$query = '?user_id='.$user->id.'&cursor=1&count=10';
    $url = 'https://api.twitter.com/1.1/followers/list.json';
    $list = $twitter->setGetfield($query)->buildOauth($url, 'GET')->performRequest();
    //print_r(json_decode($list));
	
	array_merge($users, json_decode($list)->users);
	
	return $users;
}

function getSeedUsers($twitter, $letters, $numUsers=26){
	$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	$usersPerLetter = $numUsers/26;
	$stepBetweenUsers=2;
	$usersToRequest = $usersPerLetter * $stepBetweenUsers;

	$users = array();
	$letter = 'A';
	foreach($letters as $letter) {
		$usersForLetter = getUsersForLetter($twitter, $letter, $usersToRequest);
		$selectedUsers = selectUsersByStep($usersForLetter, $usersPerLetter, $stepBetweenUsers);
	   $users = array_merge((array)$users, (array)$selectedUsers);
	}

	echo 'Retrieved '.count($users).' users from searching by letters'."\n";
	return $users;
}

function getUsersForLetter($requestObject, $letter, $usersToRequest) {
	echo 'Requesting '.$usersToRequest.': '.$letter.' ...';
	$searchURL = 'https://api.twitter.com/1.1/users/search.json';
	$requestMethod = 'GET';
	$resultsForLetter = array();
	
	$count = min($usersToRequest, 20);
	for ($page = 0; $page < 50; $page++){
		$getfield = '?q='.$letter.'&count='.$count.'&page='.$page;
		$result = json_decode($requestObject->setGetfield($getfield)->buildOauth($searchURL, $requestMethod)->performRequest());
		$resultsForLetter = array_merge((array)$resultsForLetter, (array)$result);
		if(count($resultsForLetter) >= $usersToRequest){
			break;
		}
	}

	echo 'Received '.count($resultsForLetter).' Users'."\n";
	return $resultsForLetter;
}

function selectUsersByStep($users,$numOfUsersToSelect, $step){
	$numOfUsersNeeded = $numOfUsersToSelect*$step;
	if (count($users)<$numOfUsersNeeded) {
		echo "Not Enough Users to Select From";
		return;
	}

	$selectedUsers = array();
	for($i=0;$i<$numOfUsersNeeded;$i = $i+$step){
		array_push($selectedUsers,$users[$i]);
	}
	return $selectedUsers;
}

function printUsers($array) {
	print_r($array);
}

function parseDateAndUrl($user) {
	$locationStr = $user->location;
	$arry = explode(", ", $locationStr);
	$city = $arry[0];
	$state = $arry[1];
	$user->city = $city;
	$user->state=$state;
	
	if ($user->url == null) {
		$user->hasUrl = 0;
	}
	else {
		$user->hasUrl = 1;
	}
}

function addDateAttributesToUser($user) {
    $dateStr = $user->created_at;
    $strArray = explode(' ',$dateStr);
    $day = $strArray[0];
    $month = $strArray[1];
    $date = $strArray[2];
    $year = $strArray[5];
    $user ->created_day = $day;
    $user ->created_month = $month;
    $user ->created_date = $date;
    $user ->created_year = $year;
}

// ***********************
// Old test code
// ***********************



//    $testURL = 'https://api.twitter.com/1.1/users/search.json';
//    $testgetfield = '?q=A&count=1&page=2';
//    $json = $twitter->setGetfield($testgetfield)->buildOauth($testURL, 'GET')->performRequest();
//    $testUsers = json_decode($json);
//    foreach($testUsers as $user){
//        addDateAttributesToUser($user);
//        echo json_encode($user);
//    }

?>
