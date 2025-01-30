<?php

$user_link = parse_url($_SERVER['REQUEST_URI'])["path"];

switch($user_link){

    case "/POST_PH":
        require("code/connection.php");
        require("code/API/post_ph.php");
        break;
    default:
        http_response_code(404);
        break;
}
?>