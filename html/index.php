<?php
    date_default_timezone_set('Europe/London');
    $now = new DateTime();

    require_once "../config.inc.php";
    require_once "../../api.chrisburnell.com/functions/helpers.php";
    require_once "../functions/helpers.php";
    require_once "../functions/github.php";
    require_once "../functions/mastodon.php";
    require_once "../functions/twitter.php";
    require_once "../functions/darksky.php";

    if (!isset($_GET["q"])) {
        require_once "../../api.chrisburnell.com/layouts/header.php";
    }

    require_once "../includes/normalise.php";
    require_once "../includes/queries.php";
    require_once "../includes/authentication.php";
    require_once "../includes/post-attributes.php";
    require_once "../includes/syndication.php";
    require_once "../includes/scopes.php";

    if (!isset($_GET["q"])) {
        require_once "../../api.chrisburnell.com/layouts/footer.php";
    }
?>
