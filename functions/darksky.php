<?php

namespace Corvus;

class Darksky {

    public static function getWeather($latitude, $longitude) {
        $url = "https://api.darksky.net/forecast/" . \Config::$darksky["token"] . "/$latitude,$longitude?units=" . \Config::$darksky["units"];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (!$response) {
            curl_close($ch);
            return null;
        }
        else {
            curl_close($ch);
            $array = json_decode($response, true);
            return $array;
        }
    }

}
