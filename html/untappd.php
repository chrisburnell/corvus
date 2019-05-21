<?php

date_default_timezone_set('Europe/London');
$now = new DateTime();

require_once "../config.inc.php";
require_once "../../api.chrisburnell.com/functions/helpers.php";
require_once "../functions/helpers.php";
require_once "../functions/darksky.php";

// Normalise input
// Formats JSON-encoded content to POST-type content
if (!$_POST) {
    $_POST = json_decode(file_get_contents('php://input'), true);
}

$_HEADERS = array();
foreach(\API\getallheaders() as $name => $value) {
    $_HEADERS[$name] = $value;
}

if (!isset($_HEADERS['Authorization'])) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
    echo 'Missing "Authorization" header.';
    exit;
}
elseif ($_HEADERS['Authorization'] != \Corvus\Config::$untappd['token']) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
    echo 'Invalid "Authorization" header.';
    exit;
}

$title = !empty($_POST['title']) ? $_POST['title'] : null;
$rating = !empty($_POST['rating']) ? $_POST['rating'] : null;
$bid = !empty($_POST['bid']) ? $_POST['bid'] : null;
$authors = !empty($_POST['authors']) ? $_POST['authors'] : null;
$date = !empty($_POST['date']) ? $_POST['date'] : null;
$slug = !empty($_POST['slug']) ? $_POST['slug'] : null;
$id = !empty($_POST['id']) ? $_POST['id'] : null;
$checkin = '';
if (!empty($_POST['checkin'])) {
    $checkin = "\ncheckin:\n  name: " . $_POST['checkin'];
}
$weather = '';
if ($_POST['checkin'] == "The Griffin") {
    $weatherData = getWeather('51.5248105', '-0.0842363');
    $weather = "\nweather:";
    if ($weatherData["currently"]["summary"]) {
        $weather .= "\n  summary: " . $weatherData["currently"]["summary"];
    }
    if ($weatherData["currently"]["apparentTemperature"]) {
        $weather .= "\n  temperature: " . $weatherData["currently"]["apparentTemperature"];
    }
}
$cover = '';
if (!empty($_POST['cover'])) {
    $cover_array = explode(",", $POST['cover']);
    if (count($cover_array) > 0) {
        $cover = "\ncover: " . $cover_array[0];
    }
}
$badges = '';
$badges_ids = '';
$count = -1;
if (!empty($_POST['badges'])) {
    $badges_array = explode(",", $_POST['badges']);
    $badges_ids_array = explode(",", $_POST['badges_ids']);
    if (count($badges_array) > 0) {
        $badges = "\nbadges:";
        foreach($badges_array as $badge) {
            $count++;
            $badges .= "\n- ";
            if ($badges_ids_array[$count]) {
                $badges .= "name: " . trim($badge);
                $badges .= "\n  id: " . trim($badges_ids_array[$count]);
            }
            else {
                $badges .= trim($badge);
            }
        }
    }
}

$template = "---
date: $date
title: \"$title\"
authors: $authors
rating: $rating
link: https://untappd.com/b/$slug/$bid$checkin$weather$cover$badges
posting_method: https://ownyourpint.chrisburnell.com/
---
";

$data = array(
    'message' => 'Beer Check-in: ' . $title,
    'committer' => array(
        'name' => \Corvus\Config::$github['name'],
        'email' => \Corvus\Config::$github['email']
    ),
    'content' => base64_encode($template),
);

$github_api_url = 'https://api.github.com/repos/' . \Corvus\Config::$github['username'] . '/' . \Corvus\Config::$github['repository'] . '/contents/_posts/beer/' . $now->format('Y-m-d') . '-' . $id . '.md';
$GITHUB_curl = curl_init($github_api_url);
curl_setopt($GITHUB_curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($GITHUB_curl, CURLOPT_HTTPHEADER, array('User-Agent: ' . \Corvus\Config::$name, 'Authorization: token ' . \Corvus\Config::$github['token']));
curl_setopt($GITHUB_curl, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($GITHUB_curl, CURLOPT_POSTFIELDS, json_encode($data));

$GITHUB_response = curl_exec($GITHUB_curl);
if (!$GITHUB_response) {
    curl_close($GITHUB_curl);
    echo 'Failed to publish to GitHub.';
    exit;
}
else {
    echo \Corvus\Config::$site_url . 'beer/' + $id + '/';
    curl_close($GITHUB_curl);
}

header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');
header('Location: https://chrisburnell.com/201.html?q=' . \Corvus\Config::$site_url . 'beer/' + $id + '/');

?>
