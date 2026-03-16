<?php

require_once __DIR__ . "/../config/apis.php";

function searchAnime($query){

    $url = JIKAN_BASE_URL."/anime?q=".urlencode($query);

    return callAPI($url);
}