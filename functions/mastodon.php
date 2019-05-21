<?php

namespace Corvus;

class Mastodon {

    public static function post($data, $now, $post_to_twitter, $post_to_github) {
        $headers = [
            "Authorization: Bearer " . \Corvus\Config::$mastodon["token_access"]
        ];

        $media_ids = array();

        if ($data->photo) {
            $media_data = array(
                "file" => $data->photo
            );
            $ch_media = curl_init();
            curl_setopt($ch_media, CURLOPT_URL, \Corvus\Config::$mastodon["url"] . "/api/v1/media");
            curl_setopt($ch_media, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch_media, CURLOPT_POST, 1);
            curl_setopt($ch_media, CURLOPT_POSTFIELDS, $media_data);
            curl_setopt($ch_media, CURLOPT_RETURNTRANSFER, true);
            $output_media = json_decode(curl_exec($ch_media));
            curl_close ($ch_media);
            array_push($media_ids, $output_media["id"]);
        }

        $status_data = array(
            "status" => convert_twitter_handles(trim($data->content . " " . $data->bookmark_of)),
            "in_reply_to_id" => $data->$in_reply_to,
            "visibility" => "public",
            "media_ids[]" => $media_ids
        );

        $ch_status = curl_init();
        curl_setopt($ch_status, CURLOPT_URL, \Corvus\Config::$mastodon["url"] . "/api/v1/statuses");
        curl_setopt($ch_status, CURLOPT_POST, 1);
        curl_setopt($ch_status, CURLOPT_POSTFIELDS, $status_data);
        curl_setopt($ch_status, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_status, CURLOPT_HTTPHEADER, $headers);
        $output_status = json_decode(curl_exec($ch_status));
        curl_close ($ch_status);

        // Pass on the details to Twitter or GitHub
        if ($post_to_twitter) {
            \Corvus\Twitter::post($data, $now, $output_status["uri"], $post_to_github);
        }
        else if ($post_to_github) {
            \Corvus\GitHub::post($data, $now, $output_status["uri"], null);
        }
    }

}
