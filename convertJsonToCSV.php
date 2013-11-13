<?php

// Get cmd args
if (empty($argv[1])) die("arg 1 should be the name of the json file, and arg 2 the name of the ouput csv file\n");
$jsonFilename = $argv[1];
$csvFilename = $argv[2];

// Read in and decode json file 
$json = file_get_contents($jsonFilename);
$jsonArray = json_decode($json, true);

// Open csv file
$f = fopen($csvFilename, 'w');

// Write first line keys
fputcsv($f, array_keys($jsonArray[0]));

// Write data
foreach ($jsonArray as $line)
{	
	fputs($f, implode(", ", array_values($line)));
	fputs($f, "\n");
}
fclose($f);

