<?php

namespace Corvus;

class Helpers {

    public static function create_webmention_data($now, $content, $source) {
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

}
