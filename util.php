<?php

function getFinalUrl($url){
    // Unshortens the urls by following the redirections.
    // Found at http://jonathonhill.net/2012-05-18/unshorten-urls-with-php-and-curl/
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_FOLLOWLOCATION => TRUE,  // the magic sauce
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_SSL_VERIFYHOST => FALSE, // suppress certain SSL errors
        CURLOPT_SSL_VERIFYPEER => FALSE, 
    ));
    curl_exec($ch);
    $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return $url;
}

function getTitle($url){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5); 

    $content = curl_exec($ch);
    
    curl_close($ch);
    $title = '';
    if(strlen($content) > 0 && preg_match('/\<title\b.*\>(.*)\<\/title\>/i', $content, $matches)){
        $title = trim($matches[1]);
    }

    return $title;
}