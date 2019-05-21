<?php

namespace Corvus;

class GitHub {

    public static function post($data, $now, $mastodon_uri, $twitter_slug) {
        // Set up injected YAML-y content
        $yaml_draft = "";
        $yaml_category = "";
        $yaml_title = "";
        $yaml_lede = "";
        $yaml_date = "";
        $yaml_tags = "";
        $yaml_audience = "";
        $yaml_checkin = "";
        $yaml_weather = "";
        $yaml_bookmark_of = "";
        $yaml_like_of = "";
        $yaml_in_reply_to = "";
        $yaml_rsvp = "";
        $yaml_syndicate_to = "";
        $yaml_photo = "";
        $yaml_posting_method = "";
        $yaml_hide = "";
        $yaml_content = "";

        // Determine the Title
        $title = isset($data->name) ? $data->name : (isset($data->slug) ? $data->slug : $now->format("U"));

        // Determine the Slug
        $file_name = $now->format("Y-m-d") . "-" . \API\slugify($title) . ".md";

        // Determine the Status
        if ($data->status === "draft") {
            $yaml_draft = "\ndraft: true";
        }

        // Article Content
        if ($data->category === "article" or $data->category === "coffee") {
            $yaml_title = "\ntitle: \"$title\"";
        }

        // Bookmark Content
        if ($data->category === "bookmark") {
            $yaml_title = "\ntitle: \"$title\"";
            if (isset($data->content)) {

                $yaml_lede = "\nlede: \"" . trim(\API\convert_content(str_replace('"', '\"', $data->content), \Config::$site_url)) . "\"";
            }
        }
        else {
            $yaml_content = $data->content;
        }

        // Date
        $yaml_date = "\ndate: " . $now->format("Y-m-d H:i:s");

        // RSVP
        if (isset($data->rsvp)) {
            $yaml_rsvp = "\nrsvp: \"" . $data->rsvp . "\"";
            array_push($data->tags, "rsvp");
        }

        // Process Tags
        if (isset($data->tags) and !empty($data->tags[0])) {
            $yaml_tags = "\ntags:";
            if (is_array($data->tags)) {
                foreach ($data->tags as $tag) {
                    $yaml_tags .= "\n- " . $tag;
                    if ($tag === "indieweb") {
                        $yaml_audience = "audience:\n- IndieWeb";
                    }
                }
            }
            else {
                $yaml_tags .= "\n- " . $data->tags;
                if ($data->tags === "indieweb") {
                    $yaml_audience = "audience:\n- IndieWeb";
                }
            }
        }

        // Checkin
        if (isset($data->checkin)) {
            // expected format:
            // geo:123.45,-123.45;u=20
            preg_match_all("/-?\d*\.{0,1}\d+/", $data->checkin, $matches);
            list($latitude, $longitude) = $matches[0];
            $url = "https://atlas.p3k.io/map/img?marker[]=lat:" . $latitude . ";lng:" . $longitude . ";icon:dot-small-gray&basemap=gray&width=810&height=200";
            copy($url, \Config::$static_path . "map_" . $title . ".png");
            $yaml_checkin = "\ncheckin:\n  geo: " . $latitude . "," . $longitude . "\n  image: map_" . $title . ".png";
            $weather = \Corvus\Darksky::getWeather($latitude, $longitude);
            $yaml_weather = "\nweather:";
            if ($weather["currently"]["summary"]) {
                $yaml_weather .= "\n  summary: " . $weather["currently"]["summary"];
            }
            if ($weather["currently"]["apparentTemperature"]) {
                $yaml_weather .= "\n  temperature: " . $weather["currently"]["apparentTemperature"];
            }
        }

        // Bookmark
        if (isset($data->bookmark_of)) {
            $yaml_bookmark_of = "\nbookmark_of: " . $data->bookmark_of;
        }

        // Like
        if (isset($data->like_of)) {
            $yaml_like_of = "\nlike_of: " . $data->like_of;
        }

        // Reply
        if (isset($data->in_reply_to)) {
            $yaml_in_reply_to = "\nin_reply_to:\n- " . $data->in_reply_to;
            if (empty($data->rsvp) or !isset($data->rsvp)) {
                $yaml_hide = "\nhidden: true";
            }
        }

        // Syndication Targets
        if ((isset($mastodon_uri) and !empty($mastodon_uri)) or (isset($twitter_slug) and !empty($twitter_slug))) {
            $yaml_syndicate_to = "\nsyndicate_to:";
            if (isset($mastodon_uri) and !empty($mastodon_uri)) {
                $yaml_syndicate_to .= "\n- " . $mastodon_uri;
            }
            if (isset($twitter_slug) and !empty($twitter_slug)) {
                $yaml_syndicate_to .= "\n- https://twitter.com/" . \Config::$twitter["username"] . "/status/" . $twitter_slug;
            }
        }

        // Photos
        if (isset($photo) and !empty($photo)) {
            $yaml_photo = "\nphoto:";
            if (is_array($photo)) {
                foreach($photo as $single_photo) {
                    if (strpos($single_photo, "http") === false) {
                        $single_photo = str_replace(\Config::$static_path, \Config::$site_url . "static/", $single_photo);
                    }
                    $yaml_photo .= "\n- " . $single_photo;
                }
            }
            else {
                if (strpos($photo, "http") === false) {
                    $photo = str_replace(\Config::$static_path, \Config::$site_url . "static/", $photo);
                }
                $yaml_photo .= "\n- " . $photo;
            }
        }

        // Posting Method
        if (isset($data->posting_method)) {
            $yaml_posting_method = "\nposting_method: " . $data->posting_method;
        }

        // Compose the file contents
        if ($data->category === "like") {
            $github_content = "---" . $yaml_date . $yaml_like_of . $yaml_posting_method . $yaml_hide . "\n---\n";
        }
        else {
            $github_content = "---" . $yaml_draft . $yaml_category . $yaml_date . $yaml_title . $yaml_lede . $yaml_tags . $yaml_checkin . $yaml_weather . $yaml_bookmark_of . $yaml_in_reply_to . $yaml_rsvp . $yaml_syndicate_to . $yaml_posting_method . $yaml_hide . $yaml_photo . "\n---\n" . \API\convert_content($yaml_content, \Config::$site_url) . "\n";
        }

        // GitHub Commit Data
        $publish_data = array(
            "message" => "New " . ucfirst($data->category) . ": " . $file_name,
            "committer" => array(
                "name" => \Config::$github["name"],
                "email" => \Config::$github["email"]
            ),
            "content" => base64_encode($github_content),
        );

        // PUT the data on GitHub
        $url = \Config::$site_url . $data->category . "/" . \API\slugify($title) . "/";
        $github_api_url = "https://api.github.com/repos/" . \Config::$github["username"] . "/" . \Config::$github["repository"] . "/contents/" . ($data->status === "draft" ? "_drafts" : "_posts/" . $data->directory) . "/" . $file_name;
        $GITHUB_curl = curl_init($github_api_url);
        curl_setopt($GITHUB_curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($GITHUB_curl, CURLOPT_HTTPHEADER, array("User-Agent: " . \Config::$name, "Authorization: token " . \Config::$github["token"]));
        curl_setopt($GITHUB_curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($GITHUB_curl, CURLOPT_POSTFIELDS, json_encode($publish_data));

        // Handle response from GitHub
        $GITHUB_response = curl_exec($GITHUB_curl);
        if (!$GITHUB_response) {
            curl_close($GITHUB_curl);
            header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
            echo "Failed to publish to GitHub.";
            exit;
        }
        else {
            curl_close($GITHUB_curl);
            $content_and_links = $data->content . (isset($data->in_reply_to) ? " " . $data->in_reply_to : "") . (isset($data->bookmark_of) ? " " . $data->bookmark_of : "") . (isset($data->like_of) ? " " . $data->like_of : "") . (isset($data->read_of) ? " " . $data->read_of : "");
            \Corvus\Helpers::create_webmention_data($now, $content_and_links, $url);
            header($_SERVER["SERVER_PROTOCOL"] . " 201 Created");
            header("Location: https://chrisburnell.com/201.html?q=$url");
            // header("Location: " . $url);
            exit;
        }
    }

    public static function update($url, $content) {
        $url_split = explode("/", rtrim($url, "/"));
        $slug = end($url_split);
        $category = $url_split[count($url_split)-2];
        $directory = "notes";
        switch ($category) {
            case "article":
                $directory = "articles";
                break;
            case "bookmark":
                $directory = "bookmarks";
                break;
            case "like":
                $directory = "likes";
                break;
            case "book":
                $directory = "books";
                break;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/" . \Config::$github["username"] . "/" . \Config::$github["repository"] . "/contents/_posts/" . $directory);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: " . \Config::$name, "Authorization: token " . \Config::$github["token"]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $array = json_decode($response, true);

        foreach($array as $post) {
            if (strpos($post["name"], $slug) !== false) {
                $lookup_url = $post["url"];
                break;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $lookup_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: " . \Config::$name, "Authorization: token " . \Config::$github["token"]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $array = json_decode($response, true);
        $file_name = $array["name"];
        $sha = $array["sha"];
        $content = $content . "\r\n";

        $publish_data = array(
            "message" => "Update: " . $file_name,
            "committer" => array(
                "name" => \Config::$github["name"],
                "email" => \Config::$github["email"]
            ),
            "content" => base64_encode($content),
            "sha" => $sha
        );

        $github_api_url = "https://api.github.com/repos/" . \Config::$github["username"] . "/" . \Config::$github["repository"] . "/contents/_posts/" . $directory . "/" . $file_name;
        $GITHUB_curl = curl_init($github_api_url);
        curl_setopt($GITHUB_curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($GITHUB_curl, CURLOPT_HTTPHEADER, array("User-Agent: " . \Config::$name, "Authorization: token " . \Config::$github["token"]));
        curl_setopt($GITHUB_curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($GITHUB_curl, CURLOPT_POSTFIELDS, json_encode($publish_data));

        $GITHUB_response = curl_exec($GITHUB_curl);
        if (!$GITHUB_response) {
            curl_close($GITHUB_curl);
            header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
            echo "Failed to update to GitHub.";
            exit;
        }
        else {
            curl_close($GITHUB_curl);
            header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
            header("Location: $url");
            exit;
        }
    }

    public static function delete($url) {
        $url_split = explode("/", rtrim($url, "/"));
        $slug = end($url_split);
        $category = $url_split[count($url_split)-2];
        $directory = "notes";
        switch ($category) {
            case "article":
                $directory = "articles";
                break;
            case "bookmark":
                $directory = "bookmarks";
                break;
            case "like":
                $directory = "likes";
                break;
            case "book":
                $directory = "books";
                break;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/" . \Config::$github["username"] . "/" . \Config::$github["repository"] . "/contents/_posts/" . $directory);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: " . \Config::$name, "Authorization: token " . \Config::$github["token"]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $array = json_decode($response, true);

        foreach($array as $post) {
            if (strpos($post["name"], $slug) !== false) {
                $lookup_url = $post["url"];
                break;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $lookup_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: " . \Config::$name, "Authorization: token " . \Config::$github["token"]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $array = json_decode($response, true);
        $file_name = $array["name"];
        $sha = $array["sha"];

        $publish_data = array(
            "message" => "Delete: " . $file_name,
            "committer" => array(
                "name" => \Config::$github["name"],
                "email" => \Config::$github["email"]
            ),
            "sha" => $sha
        );

        $github_api_url = "https://api.github.com/repos/" . \Config::$github["username"] . "/" . \Config::$github["repository"] . "/contents/_posts/" . $directory . "/" . $file_name;
        $GITHUB_curl = curl_init($github_api_url);
        curl_setopt($GITHUB_curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($GITHUB_curl, CURLOPT_HTTPHEADER, array("User-Agent: " . \Config::$name, "Authorization: token " . \Config::$github["token"]));
        curl_setopt($GITHUB_curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($GITHUB_curl, CURLOPT_POSTFIELDS, json_encode($publish_data));

        $GITHUB_response = curl_exec($GITHUB_curl);
        if (!$GITHUB_response) {
            curl_close($GITHUB_curl);
            header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
            echo "Failed to delete on GitHub.";
            exit;
        }
        else {
            curl_close($GITHUB_curl);
            header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
            header("Location: $url");
            exit;
        }
    }

}
