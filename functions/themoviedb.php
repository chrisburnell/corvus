<?php

// Workflow:
// 1. Get configuration
//    https://api.themoviedb.org/3/configuration?api_key=123
//    `secure_base_url`
// 2. Search for TV/Movie by Title
//    https://api.themoviedb.org/3/search/tv?api_key=123&query=a+b+c
//    `id`
// 3. Search for TV/Movie Episode by ID
//    https://api.themoviedb.org/3/tv/999/season/999/episode/999?api_key=123&language=en-GB
//    `still_path`
// 4. Build URL to cover
//    `secure_base_url` + `still_path`

namespace Corvus;

class TMDB {

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.themoviedb.org/3/tv/1399/season/5/episode/5?language=en-US&api_key=661f7bcf726d652707d9a1d9af18cf0b",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "{}",
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
    }

}
