<?php
// Thanks to Stack Overflow user, Kostanos, for the basic idea for this script!

// Get cmd args
if (empty($argv[1])) die("arg 1 should be the name of the json file, and arg 2 the name of the ouput csv file\n");
$jsonFilename = $argv[1];
$csvFilename = $argv[2];

// Read in and decode json file 
$json = file_get_contents($jsonFilename);
$jsonArray = json_decode($json, true);
//$f = fopen('php://output', 'w');


// FirstLineKeys are the keys that you want to pull from the json. 
// If it is false, it will pull all first level keys
//$firstLineKeys = array(); 
$firstLineKeys = false; 


// Open and write to csv
$f = fopen($csvFilename, 'w');
foreach ($jsonArray as $line)
{
    if (empty($firstLineKeys))
    {
        $firstLineKeys = array_keys($line);
        fputcsv($f, $firstLineKeys);
        $firstLineKeys = array_flip($firstLineKeys);
    }
    // Using array_merge is important to maintain the order of keys acording to the first element
    fputcsv($f, array_merge($firstLineKeys, $line));
}

fclose($f);

