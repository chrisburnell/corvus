<?php

if (!function_exists("getallheaders")) {
    function getallheaders() {
       $headers = array();
       foreach ($_SERVER as $name => $value) {
           if (substr($name, 0, 5) == "HTTP_") {
               $headers[str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
}

function slugify($text) {
    // Strip apostrophes
    $text = str_replace("’", "", $text);
    // replace non letter or digits by -
    $text = preg_replace("~[^\pL\d]+~u", "-", $text);
    // transliterate
    $text = iconv("utf-8", "us-ascii//TRANSLIT", $text);
    // remove unwanted characters
    $text = preg_replace("~[^-\w]+~", "", $text);
    // trim
    $text = trim($text, "-");
    // remove duplicate -
    $text = preg_replace("~-+~", "-", $text);
    // lowercase
    $text = strtolower($text);
    if (empty($text)) {
        return "";
    }
    return $text;
}

function convert_urls($text) {
    $pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";

    if (strpos($text, Config::$site_url) !== false) {
        return preg_replace($pattern, "<a href=\"$1\">$1</a>", $text);
    }
    else {
        return preg_replace($pattern, "<a href=\"$1\" rel=\"external\">$1</a>", $text);
    }
}

function convert_hashtags($text) {
    $pattern = "/#(\\w+)/";
    return preg_replace($pattern, "<a href=\"https://twitter.com/hashtag/$1\" rel=\"external\">#$1</a>", $text);
}

function convert_quotes($text) {
    $modified = str_replace("“", "<q>", $text);
    $modified = str_replace("‘", "<q>", $modified);
    $modified = str_replace("”", "</q>", $modified);
    $modified = str_replace("’", "</q>", $modified);
    return str_replace("'", "’", $modified);
}

function strip_newlines($text) {
    return str_replace(array("\r", "\n"), "", $text);
}

function convert_content($text) {
    if (!empty($text)) {
        $text = "\n$text\n";
    }
    return convert_quotes(convert_hashtags(convert_urls($text)));
}

function convert_twitter_handles($text) {
    $pattern = "/@(\\w+)/";
    return preg_replace($pattern, "@$1@twitter.com", $text);
}

function create_webmention_data($now, $content, $source) {
    $pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
    preg_match_all($pattern, $content, $targets);

    $json = file_get_contents("../../chrisburnell.com/telegraph.json");

    $json_decoded = json_decode($json, true);
    $json_decoded["timestamp_modified"] = $now->format("U");
    $json_decoded["source"] = $source;
    $json_decoded["targets"] = $targets[0];

    $json_encoded = json_encode($json_decoded, JSON_UNESCAPED_SLASHES);

    file_put_contents("../../chrisburnell.com/telegraph.json", $json_encoded);
    file_put_contents("../../chrisburnell.com/sent/" . $now->format("U") . ".json", $json_encoded);
}

?>
