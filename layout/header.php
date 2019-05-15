<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
    <title><?php echo Config::$name ?></title>
    <link rel="mask-icon" href="/favicon.svg" color="#5f8aa6">
    <link rel="author" href="/humans.txt">
    <link rel="manifest" type="application/manifest+json" href="/manifest.json">
    <meta name="author" content="<?php echo Config::$author ?>">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="application-name" content="<?php echo Config::$author ?>">
    <meta name="apple-mobile-web-app-title" content="<?php echo Config::$author ?>">
    <meta name="msapplication-starturl" content="<?php echo Config::$host_url ?>">
    <meta name="description" content="<?php echo Config::$lede ?>">
    <meta name="theme-color" content="#5f8aa6">
    <meta name="msapplication-navbutton-color" content="#5f8aa6">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta http-equiv="cleartype" content="on">

    <link rel="canonical" href="<?php echo Config::$host_url ?>">

    <meta name="twitter:card" content="summary">
    <meta name="twitter:creator" content="@<?php echo Config::$twitter["username"] ?>">
    <meta name="twitter:title" content="<?php echo Config::$name ?>">
    <meta name="twitter:description" content="<?php echo Config::$lede ?>">
    <meta name="twitter:image" content="<?php echo Config::$site_url ?>images/avatar@2x.png">
    <meta name="twitter:domain" content="<?php echo rtrim(str_replace("https://", "", Config::$host_url), "/") ?>">

    <meta property="og:type" content="website">
    <meta property="og:description" content="<?php echo Config::$lede ?>">
    <meta property="og:locale" content="en">
    <meta property="og:url" content="<?php echo Config::$host_url ?>">
    <meta property="og:image" content="<?php echo Config::$site_url ?>images/avatar@2x.png">

    <link rel="stylesheet" href="https://chrisburnell.com/css/main.min.css">

    <style>
        :root {
            --color-raven: #3b5567;
        }
    </style>
</head>
<body>
    <a class="hidden" href="#content">Skip to main content</a>

    <header class="wrap" role="banner">
        <a rel="home" class="logo" href="<?php echo Config::$host_url ?>" title="<?php echo Config::$author ?>">
            <svg class="icon  icon--raven" role="img" title="<?php echo Config::$author ?>" aria-labelledby="svg-header">
                <title id="svg-header"><?php echo Config::$author ?></title>
                <use xlink:href="/sprites.svg#svg--raven"></use>
            </svg>
        </a>
        <nav class="nav-primary" role="navigation">
            <ol class="nav-primary-list" role="list">
                <li role="listitem">
                    <a href="https://chrisburnell.com/" rel="external">My Homepage</a>
                </li>
            </ol>
        </nav>
    </header>

    <main class="wrap  stretch" role="main">
        <section class="content  content--full" id="content">
            <div class="content__body">
                <h1 class="title" role="heading"><?php echo Config::$name ?></h1>
                <h2 class="lede"><?php echo Config::$lede ?></h2>
