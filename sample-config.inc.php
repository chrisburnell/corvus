<?php

class Config {
    public static $name = 'Example';
    public static $lede = "Lorem ipsum";
    public static $author = "Jane Doe";
    public static $site_url = 'https://example.com/';
    public static $host_url = 'https://micropub.example.com/';
    public static $token_endpoint = 'https://tokens.indieauth.com/token';
    public static $static_path = '/static/';

    public static $mastodon = [
        'username' =>     'username',
        'url' =>          'example.com',
        'access_token' => ''
    ];

    public static $twitter = [
        'username' =>            'username',
        'consumer_key' =>        '',
        'consumer_secret' =>     '',
        'access_token' =>        '',
        'access_token_secret' => ''
    ];

    public static $github = [
        'username' =>   'username',
        'name' =>       'name',
        'email' =>      'name@example.com',
        'token' =>      '',
        'repository' => 'repository'
    ];

    public static $lastfm = [
        'username' =>      'username',
        'api_key' =>       '',
        'shared_secret' => ''
    ];

    public static $telegraph = [
        'token' => ''
    ];

    public static $untappd = [
        'username' => '',
        'token' =>    '',
    ];

    public static $darksky = [
        'token' => '',
        'units' => 'ca'
    ];

    public static $omdb = [
        'token' => ''
    ];

    public static $themoviedb = [
        'token' => '',
        'token_v4' => ''
    ];
};

?>
