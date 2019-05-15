<?php

// Normalise input
// Formats JSON-encoded content to POST-type content
if (!$_POST) {
    $_POST = json_decode(file_get_contents("php://input"), true);
}
// Push Headers into an array
$_HEADERS = array();
foreach(getallheaders() as $name => $value) {
    $_HEADERS[$name] = $value;
}

?>
