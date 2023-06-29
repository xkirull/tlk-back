<?php

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);

// Disable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

require_once("./db.php");
require_once("./api.php");

new DB();

// Максимальное кол-вл постов
$maxArticleCount = 4;

$request = explode("?", $_SERVER["REQUEST_URI"])[0];
$method = $_SERVER["REQUEST_METHOD"];

$auth = ["email" => $_COOKIE["email"] ?? "", "password" => $_COOKIE["password"] ?? ""];

if ($method === "GET") {
    switch ($request) {
        case "/api/getArticle":
            getArticle($maxArticleCount, $_GET["page"] ?? 1, $_GET["search"] ?? "");
            break;
        case "/api/getArticlePageCount":
            getArticlePageCount($maxArticleCount, $_GET["search"] ?? "");
            break;
    }
}

if ($method === "POST") {
    switch ($request) {
        case "/api/registration":
            registration($_POST["email"], $_POST["password"]);
            break;

        case "/api/authorization":
            authorization($_POST["email"], $_POST["password"]);
            break;

        case "/api/restorePassword":
            restorePassword($_POST["email"], $_POST["password"]);
            break;
    }
}
