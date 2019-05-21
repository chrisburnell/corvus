<?php

// Parse POSTed Data
$name = !empty($_POST["name"]) ? $_POST["name"] : null;
$bookmark_of = !empty($_POST["bookmark-of"]) ? $_POST["bookmark-of"] : null;
$like_of = !empty($_POST["like-of"]) ? $_POST["like-of"] : null;
$listen_of = !empty($_POST["listen-of"]) ? $_POST["listen-of"] : null;
$read_of = (!empty($_POST["properties"]["read-of"]) ? $_POST["properties"]["read-of"] : (!empty($_POST["read-of"]) ? $_POST["read-of"] : null));
$watch_of = (!empty($_POST["properties"]["watch-of"]) ? $_POST["properties"]["watch-of"] : (!empty($_POST["watch-of"]) ? $_POST["watch-of"] : null));
$pk_ate = (!empty($_POST["properties"]["name"]) ? $_POST["properties"]["name"] : (!empty($_POST["pk-ate"]) ? $_POST["pk-ate"] : null));
$pk_drank = (!empty($_POST["properties"]["name"]) ? $_POST["properties"]["name"] : (!empty($_POST["pk-drank"]) ? $_POST["pk-drank"] : null));
$in_reply_to = !empty($_POST["in-reply-to"]) ? $_POST["in-reply-to"] : null;
$syndicate_to = (!empty($_POST["mp-syndicate-to"]) ? $_POST["mp-syndicate-to"] : (!empty($_POST["syndicate-to"]) ? $_POST["syndicate-to"] : null));
$tags = !empty($_POST["category"]) ? $_POST["category"] : null;
$checkin = (!empty($_POST["checkin"]) ? $_POST["checkin"] : (!empty($_POST["location"]) ? $_POST["location"] : null));
$content = !empty($_POST["content"]) ? $_POST["content"] : null;
$url = !empty($_POST["url"]) ? $_POST["url"] : null;
$slug = (!empty($_POST["mp-slug"]) ? $_POST["mp-slug"] : (!empty($_POST["slug"]) ? $_POST["slug"] : null));
$rating = !empty($_POST["rating"]) ? $_POST["rating"] : null;
$isbn = !empty($_POST["isbn"]) ? $_POST["isbn"] : null;
$authors = !empty($_POST["authors"]) ? $_POST["authors"] : null;
$photo = isset($_POST["photo"]) ? $_POST["photo"] : null;
$rsvp = !empty($_POST["rsvp"]) ? $_POST["rsvp"] : null;
$status = !empty($_POST["post-status"]) ? $_POST["post-status"] : "published";

// Build Data object
$data = new stdClass();

// Populate Data object
$data->posting_method = $AUTHORIZATION_values["client_id"];
$data->name = $name;
$data->bookmark_of = $bookmark_of;
$data->like_of = $like_of;
$data->listen_of = $listen_of;
$data->read_of = $read_of;
$data->watch_of = $watch_of;
$data->pk_ate = $pk_ate;
$data->pk_drank = $pk_drank;
$data->in_reply_to = $in_reply_to;
$data->syndicate_to = $syndicate_to;
$data->tags = $tags;
$data->checkin = $checkin;
$data->content = $content;
$data->url = $url;
$data->slug = $slug;
$data->rating = $rating;
$data->isbn = $isbn;
$data->authors = $authors;
$data->photo = $photo;
$data->rsvp = $rsvp;
$data->status = $status;

// Self-referential conditionally populate Data object
$data->category = "note";
$data->directory = "notes";
if (isset($data->read_of) and !empty($data->read_of)) {
    $data->category = "book";
    $data->directory = "books";
}
elseif (isset($data->bookmark_of) and !empty($data->bookmark_of)) {
    $data->category = "bookmark";
    $data->directory = "bookmarks";
}
elseif (isset($data->pk_drank) and !empty($data->pk_drank)) {
    $data->category = "coffee";
    $data->directory = "coffee";
}
elseif (isset($data->like_of) and !empty($data->like_of)) {
    $data->category = "like";
    $data->directory = "likes";
}
elseif (isset($data->name) and !empty($data->name)) {
    $data->category = "article";
    $data->directory = "articles";
}
