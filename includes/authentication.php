<?php

if (!isset($_HEADERS["Authorization"])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
    echo "Missing 'Authorization' header.";
    // exit;
}
else if (!isset($_POST["action"]) and !isset($_POST["h"]) and !isset($_POST["type"])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing 'action', 'h', or 'type' value.";
    // exit;
}
else {
    $AUTHORIZATION_options = array(
        CURLOPT_URL => Config::$token_endpoint,
        CURLOPT_HTTPGET => TRUE,
        CURLOPT_USERAGENT => Config::$site_url,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HEADER => FALSE,
        CURLOPT_HTTPHEADER => array(
            "Content-type: application/x-www-form-urlencoded",
            "Authorization: " . $_HEADERS["Authorization"]
        )
    );
    $AUTHORIZATION_curl = curl_init();
    curl_setopt_array($AUTHORIZATION_curl, $AUTHORIZATION_options);
    $AUTHORIZATION_source = curl_exec($AUTHORIZATION_curl);
    curl_close($AUTHORIZATION_curl);

    parse_str($AUTHORIZATION_source, $AUTHORIZATION_values);

    if (!isset($AUTHORIZATION_values["me"])) {
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
        echo "Missing 'me' value in authentication token.";
        exit;
    }
    if (!isset($AUTHORIZATION_values["scope"])) {
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
        echo "Missing 'scope' value in authentication token.";
        exit;
    }
    if (substr($AUTHORIZATION_values["me"], -1) != "/") {
        $AUTHORIZATION_values["me"].= "/";
    }
    if (substr(Config::$site_url, -1) != "/") {
        Config::$site_url.= "/";
    }
    if (strtolower($AUTHORIZATION_values["me"]) != strtolower(Config::$site_url)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
        echo "Mismatching 'me' value in authentication token.";
        exit;
    }
    if (!stristr($AUTHORIZATION_values["scope"], "create") and !stristr($AUTHORIZATION_values["scope"], "update") and !stristr($AUTHORIZATION_values["scope"], "delete")) {
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
        echo "Missing 'create', 'update', or 'delete' value in 'scope'.";
        exit;
    }
    if (!isset($_POST["content"]) and !isset($_POST["bookmark-of"]) and !isset($_POST["like-of"]) and !isset($_POST["listen-of"]) and !isset($_POST["rating"]) and !isset($_POST["properties"]["read-of"]) and !isset($_POST["replace"])) {
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
        echo "Missing 'content', 'bookmark-of', 'like-of', 'listen-of', 'rating', 'read-of', or 'replace' value.";
        exit;
    }
    if (isset($_POST["h"]) and !isset($_POST["like-of"]) and (!isset($_POST["mp-syndicate-to"]) and !isset($_POST["syndicate-to"]))) {
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
        echo "Missing 'mp-syndicate-to' or 'syndicate-to' post/syndication targets.";
        exit;
    }

    // Everything’s cool if we reach this point.
}

?>
