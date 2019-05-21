<?php

// Let’s respond to some friendly queries
if (isset($_GET["q"])) {
    if ($_GET["q"] === "syndicate-to" or $_GET["q"] === "config") {
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Content-type: application/json");
        echo '{
    "syndicate-to": [
        {
            "uid": "https://github.com/chrisburnell/chrisburnell.com",
            "name": "GitHub (chrisburnell.com)",
            "service": {
                "name": "GitHub",
                "url": "https://github.com",
                "photo": "https://github.githubassets.com/favicon.ico"
            },
            "user": {
                "name": "chrisburnell",
                "url": "https://github.com/chrisburnell",
                "photo": "https://avatars1.githubusercontent.com/u/1615865?s=64&v=4"
            }
        },
        {
            "uid": "https://mastodon.social/@chrisburnell",
            "name": "Mastodon (@chrisburnell)",
            "service": {
                "name": "Mastodon",
                "url": "https://mastodon.social",
                "photo": "https://mastodon.social/favicon.ico"
            },
            "user": {
                "name": "chrisburnell",
                "url": "https://mastodon.social/@chrisburnell",
                "photo": "https://files.mastodon.social/accounts/avatars/000/218/396/original/d203bb77eaef6791.png"
            }
        },
        {
            "uid": "https://twitter.com/iamchrisburnell",
            "name": "Twitter (@iamchrisburnell)",
            "service": {
                "name": "Twitter",
                "url": "https://twitter.com",
                "photo": "https://abs.twimg.com/favicons/favicon.ico"
            },
            "user": {
                "name": "iamchrisburnell",
                "url": "https://twitter.com/iamchrisburnell",
                "photo": "https://pbs.twimg.com/profile_images/1074661742821732352/HOf4JlAr_400x400.jpg"
            }
        }
    ]
}';
        exit;
    }
    if ($_GET["q"] == "categories") {
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Content-type: application/json");
        if (isset($_GET["search"])) {
            $search = $_GET["search"];
            $matches = array_filter(\Corvus\Config::$categories, function($category) use ($search) {
                if (strpos($category, $search) !== FALSE) {
                    return true;
                }
            });
            echo '{ "categories": [ "' . implode("\",\"", $matches) . '" ] }';
        }
        else {
            echo '{ "categories": [ "' . implode("\",\"", \Corvus\Config::$categories) . '" ] }';
        }
        exit;
    }
    if (($_GET["q"] === "source" and isset($_GET["url"])) or $_GET["q"] === "last") {
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Content-type: application/json");

        if ($_GET["q"] === "last") {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, \Corvus\Config::$site_url . "search.json");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: " . \Corvus\Config::$name, "Authorization: token " . \Corvus\Config::$github["token"]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
            $array = json_decode($response, true);

            foreach($array as $page) {
                if ($page["type"] === "post") {
                    $category = $page["category"];
                    break;
                }
            }
        }
        else {
            $url_split = explode("/", rtrim($_GET["url"], "/"));
            $slug = end($url_split);
            $category = $url_split[count($url_split)-2];
        }

        $directory = "notes";
        switch ($category) {
            case "article":
                $directory = "articles";
                break;
            case "beer":
                $directory = "beer";
                break;
            case "bookmark":
                $directory = "bookmarks";
                break;
            case "book":
                $directory = "books";
                break;
            case "coffee":
                $directory = "coffee";
                break;
            case "food":
                $directory = "food";
                break;
            case "like":
                $directory = "likes";
                break;
            case "movie":
                $directory = "movies";
                break;
            case "music":
                $directory = "music";
                break;
            case "podcast":
                $directory = "podcasts";
                break;
            case "recipe":
                $directory = "recipes";
                break;
            case "talk":
                $directory = "talks";
                break;
            case "tv":
                $directory = "tv";
                break;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/" . \Corvus\Config::$github["username"] . "/" . \Corvus\Config::$github["repository"] . "/contents/_posts/" . $directory);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: " . \Corvus\Config::$name, "Authorization: token " . \Corvus\Config::$github["token"]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $array = json_decode($response, true);

        if (isset($slug)) {
            foreach($array as $page) {
                if (strpos($page["name"], $slug) !== false) {
                    $url = $page["url"];
                    break;
                }
            }
        }
        else {
            $url = end($array)["url"];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: " . \Corvus\Config::$name, "Authorization: token " . \Corvus\Config::$github["token"]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $array = json_decode($response, true);
        $content = base64_decode($array["content"]);
        preg_match("/title\: (.+)\n/", $content, $matched_title);
        array_shift($matched_title);
        preg_match("/date\: (.+)\n/", $content, $matched_date);
        array_shift($matched_date);
        preg_match_all("/- (\w+)\n/", $content, $matched_tags);
        array_shift($matched_tags);
        preg_match_all("/- ([http]{1}[A-Za-z0-9:\/\.-_]+)\n/", $content, $matched_syndication);
        array_shift($matched_syndication);
        $content = json_encode($content);

        echo '{
    "type": ["h-entry"],
    "properties": {
        "title": "' . (count($matched_title) > 0 ? str_replace("'", "", $matched_title[0]) : "") . '",
        "date": "' . $matched_date[0] . '",
        "category": ["' . $category . '"],
        "tags": [' . (count($matched_tags) > 0 ? implode("", "", $matched_tags[0]) : "") . '],
        "syndicate-to": [' . (count($matched_syndication) > 0 ? implode(", ", $matched_syndication[0]) : "") . '],
        "content": [' . $content . ']
    }
}';
        exit;
    }
}

?>
