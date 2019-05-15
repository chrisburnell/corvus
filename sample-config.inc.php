<?php

class Config {
    public static $name = "Example";
    public static $site_url = "https://example.com/";
    public static $host_url = "https://micropub.example.com/";
    public static $token_endpoint = "https://tokens.indieauth.com/token";
    public static $static_path = "/static/";
    public static $categories = array("note");
    public static $max_file_size = 5; // MB

    public static $mastodon = [
        "username" =>     "username",
        "url" =>          "example.com",
        "client_key" =>    "",
        "client_secret" => "",
        "access_token" =>  ""
    ];

    public static $twitter = [
        "username" =>            "username",
        "consumer_key" =>        "",
        "consumer_secret" =>     "",
        "access_token" =>        "",
        "access_token_secret" => ""
    ];

    public static $github = [
        "username" =>   "username",
        "name" =>       "name",
        "email" =>      "name@example.com",
        "token" =>      "",
        "repository" => "repository"
    ];

    public static $telegraph = [
        "token" => ""
    ];

    public static $untappd = [
        "username" => "",
        "token" =>    "",
    ];

    public static $darksky = [
        "token" => "",
        "units" => "ca"
    ];

    public static $omdb = [
        "token" => ""
    ];

    public static $themoviedb = [
        "token" => "",
        "token_v4" => ""
    ];

    public static $pebble = [
        "token" => ""
    ];
};

?>
