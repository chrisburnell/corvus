<?php

$post_to_github = $post_to_twitter = $post_to_mastodon = false;
if ($data->syndicate_to !== null) {
    if (is_array($data->syndicate_to)) {
        $data->syndicate_to = implode($data->syndicate_to);
    }
    if (strpos($data->syndicate_to, "mastodon") !== false) {
        $post_to_mastodon = true;
    }
    if (strpos($data->syndicate_to, "twitter") !== false) {
        $post_to_twitter = true;
    }
    if (strpos($data->syndicate_to, "github") !== false) {
        $post_to_github = true;
    }
}

// Conditional Overrides
// Automatically post to GitHub unless Post is a Note
if ($data->category !== "note") {
    $post_to_github = true;
}

// Forceful Overrides
// $post_to_github = true;
// $post_to_mastodon = true;
// $post_to_twitter = true;
