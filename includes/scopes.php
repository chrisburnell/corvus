<?php

// Handle Scopes
//   Order of Posts:
//     - Mastodon
//     - Twitter
//     - GitHub
if ((isset($_POST["action"]) and $_POST["action"] == "create") or (isset($_POST["h"]) and $_POST["h"] == "entry")) {
    // Handle uploaded photos
    if (isset($_FILES["photo"])) {
        $uploaded_file_name = (isset($_FILES["photo"]["name"]) ? $_FILES["photo"]["name"] : $_FILES["photo"]["filename"]);
        $uploaded_file_name_temporary = (isset($_FILES["photo"]["tmp_name"]) ? $_FILES["photo"]["tmp_name"] : $uploaded_file_name);

        $path_to_file = \Config::$static_path . $uploaded_file_name;

        $file_type = end(explode(".", $uploaded_file_name));
        $file_name = $now->format("U") . "." . $file_type;
        $file_size = $_FILES["photo"]["size"];
        $file_size_max = \Config::$max_file_size * 1024 * 1024;

        if ($file_size <= $file_size_max) {
            // Move the file where we want it
            if (move_uploaded_file($uploaded_file_name_temporary, $path_to_file)) {
                $data->photo = $path_to_file;
                if ($post_to_mastodon) {
                    \Corvus\Mastodon::post($data, $now, $post_to_twitter, $post_to_github);
                }
                elseif ($post_to_twitter) {
                    \Corvus\Twitter::post($data, $now, null, $post_to_github);
                }
                elseif ($post_to_github) {
                    \Corvus\GitHub::post($data, $now, null, null);
                }
                else {
                    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                    echo "Missing syndication targets.";
                    exit;
                }
            }
            else {
                header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                echo "Image upload failed!";
                exit;
            }
        }
        else {
            header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
            echo "Image is too big! Images must be <" . \Config::$max_file_size . "MB.";
            exit;
        }
    }
    // Or just post the rest of the data
    else {
        if ($post_to_mastodon) {
            \Corvus\Mastodon::post($data, $now, $post_to_twitter, $post_to_github);
        }
        elseif ($post_to_twitter) {
            \Corvus\Twitter::post($data, $now, null, $post_to_github);
        }
        elseif ($post_to_github) {
            \Corvus\GitHub::post($data, $now, null, null);
        }
        else {
            header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
            echo "Missing syndication targets.";
            exit;
        }
    }
}

if (isset($_POST["action"]) and $_POST["action"] == "update") {
    \Corvus\GitHub::update($data->url, $_POST["replace"]["content"][0]);
}

if (isset($_POST["action"]) and $_POST["action"] == "delete") {
    \Corvus\GitHub::delete($data->url);
}

if (isset($_POST["properties"]["read-of"]) and $_POST["properties"]["read-of"] and $post_to_github) {
    \Corvus\GitHub::post($data, $now, null, null);
}
