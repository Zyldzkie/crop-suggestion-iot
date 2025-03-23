<?php

$user_link = parse_url($_SERVER['REQUEST_URI'])["path"];

switch($user_link){

    case "/POST_PH":
        require("code/connection.php");
        require("code/API/post_ph.php");
        break;
    case "/":
        $page = "dashboard";
        require("code/connection.php");
        require("website/Main.php");
        break;
    case "/crop":
        $page = "crop";
        require("code/connection.php");
        require("website/Main.php");
        break;
    case "/1sthalf":
        $page = "1sthalf";
        require("code/connection.php");
        require("website/Main.php");
        break;

    case "/update":
        require("code/connection.php");
        require("website/API/update.php");
        break;
    default:
        http_response_code(404);
        break;
}
?>