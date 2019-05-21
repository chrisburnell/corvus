<?php

namespace Corvus;

require_once "../vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter {
    public static function post($data, $now, $mastodon_uri, $post_to_github) {
        // Make the connection based on the account chosen
        $connection = new TwitterOAuth(\Corvus\Config::$twitter["consumer_key"], \Corvus\Config::$twitter["consumer_secret"], \Corvus\Config::$twitter["access_token"], \Corvus\Config::$twitter["access_token_secret"]);

        // Upload the photo(s), if included
        if ($data->photo !== null) {
            $media = $connection->upload("media/upload", ["media" => $data->photo]);
            if ($data->in_reply_to !== null and strpos($data->in_reply_to, "twitter.com") !== false) {
                $parameters = [
                    "status" => trim($data->content . " " . $data->bookmark_of),
                    "in_reply_to_status_id" => substr($data->in_reply_to, strrpos($data->in_reply_to, "/") + 1),
                    "auto_populate_reply_metadata" => true,
                    "media_ids" => implode(",", [$media->media_id_string])
                ];
            }
            else {
                $parameters = [
                    "status" => trim($data->content . " " . $data->bookmark_of),
                    "media_ids" => implode(",", [$media->media_id_string])
                ];
            }
        }
        else {
            if ($data->in_reply_to !== null and strpos($data->in_reply_to, "twitter.com") !== false) {
                $parameters = [
                    "status" => trim($data->content),
                    "in_reply_to_status_id" => substr($data->in_reply_to, strrpos($data->in_reply_to, "/") + 1),
                    "auto_populate_reply_metadata" => true
                ];
            }
            else {
                $parameters = [
                    "status" => trim($data->content)
                ];
            }
        }
        $response = $connection->post("statuses/update", $parameters);

        $twitter_slug = $response->id_str;

        // Pass on the details to GitHub
        if ($connection->getLastHttpCode() == 200) {
            if ($post_to_github) {
                \Corvus\GitHub::post($data, $now, $mastodon_uri, $twitter_slug);
            }
        }
        else {
            echo "Failed to publish to Twitter.";
            exit;
        }
    }

}

?>
