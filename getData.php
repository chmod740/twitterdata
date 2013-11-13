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
		
// Save data as json file
$fp = fopen($jsonFilename, $action);
fwrite($fp, json_encode($allUsers));
fclose($fp);


// ******************************
// Function definitions
// ******************************


function getUsersFollowers($twitter, $user, $depthlimit=null){
	$users = array($user);
	
	//TODO: Impliment this function
	
	return $users;
}

function getSeedUsers($twitter, $letters, $numUsers=26){
	$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	$usersPerLetter = $numUsers/26;
	$stepBetweenUsers=2;
	$usersToRequest = $usersPerLetter * $stepBetweenUsers;

	$users = array();
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


// ***********************
// Old test code
// ***********************


////    $testURL = 'https://api.twitter.com/1.1/users/lookup.json';
////    $testgetfield = '?user_id=783214,6483922,746843,6354349';
//    $testURL = 'https://api.twitter.com/1.1/users/search.json';
//    $testgetfield = '?q=A&count=1&page=2';
//    echo $twitter->setGetfield($testgetfield)->buildOauth($testURL, 'GET')->performRequest();

?>
