<?php

require_once __DIR__ . "/../config/apis.php";

function searchTMDB($query){

    $url = TMDB_BASE_URL."/search/multi?api_key="
        .TMDB_API_KEY.
        "&query=".urlencode($query);

    return callAPI($url);
}

function getTMDBDetails($type,$id){

    $url = TMDB_BASE_URL."/$type/$id?api_key=".TMDB_API_KEY;

    return callAPI($url);
}

function callAPI($url){

    $ch = curl_init();

    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

    $response = curl_exec($ch);

    curl_close($ch);

    return json_decode($response,true);
}