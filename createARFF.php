<?PHP
    $jsonFile = './data.backup.json';
    $arffFile = './test.arff';
    // 'location','utc_offset','city','state','time_zone','profile_background_color',
    
    $attributes = array('followers_count','friends_count','listed_count','favourites_count','geo_enabled','statuses_count','lang','is_translator','profile_use_background_image','hasUrl','created_day','created_month','created_date','created_year');
    
    $realAttributes = array('followers_count','friends_count','listed_count','favourites_count','utc_offset','statuses_count');
    $nominalValues = new stdClass();
    
    // setup nominal values
    foreach($attributes as $attr) {
        if (!in_array($attr, $realAttributes)) {
            $nominalValues->$attr = array();
        }
    }
    
    $handle = fopen($jsonFile, "r");
    $print = true;
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $user = json_decode($line);
            foreach($attributes as $attr) {
                if (!in_array($attr, $realAttributes)) {
                    
                    if($user->$attr != null && !in_array($user->$attr,$nominalValues->$attr))
                        array_push($nominalValues->$attr,$user->$attr);
                }
            }
        }
    } else {
        echo 'Error';
    }
    
    // Write user data
    $fp = fopen($arffFile, 'w');
    // write header info
    fwrite($fp, '@RELATION twitterUsers'."\n\n");
    for($i = 0;$i<count($attributes);$i++) {
        $attr = $attributes[$i];
        if (!in_array($attr, $realAttributes)) {
            fwrite($fp, '@ATTRIBUTE'."\t".$attr."\t".'{');
            $first = true;
            $values =$nominalValues->$attr;
            foreach($values as $val) {
                if (!$first) {
                    fwrite($fp,',');
                    
                }
                fwrite($fp,$val);
                $first = false;
            }
            fwrite($fp,"}\n");
        } else {
            fwrite($fp, '@ATTRIBUTE'."\t".$attr."\t".'REAL'."\n");
        }
    }
    
    fwrite($fp, "\n@DATA\n");
    $handle = fopen($jsonFile, "r");
    $print = true;
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $user = json_decode($line);
            
    
            $first = true;
            for($i = 0;$i<count($attributes);$i++) {
                $attr = $attributes[$i];
                if (!$first){
                    fwrite($fp,',');
                }
                $val =$user->$attr;
                //echo $attr.','.$val."\n";
                if ($val == null){
                   fwrite($fp,'?');
                } else
                    fwrite($fp,$val);
                $first = false;
            }
            fwrite($fp, "\n");
        }
    }
    fwrite ($fp,"%\n%\n");
    fclose($fp);
    
?>