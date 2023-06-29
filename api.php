<?php

function user_verification(string $email, string $password)
{
    $password_hash = md5($password + "salt");
    $userStatus = (int) DB::getRow("SELECT COUNT(*) as count FROM users WHERE email = ? AND passwordHash = ? LIMIT 1", [$email, $password_hash])["count"];

    if ($userStatus === 0) {
        return 0;
    }

    return 1;
}

function authorization(string $email, string $password)
{
    $status = user_verification($email, $password);

    if ($status === 1) {
        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["error" => 1, "message" => "login or password is not correct"]);
    }
}


function registration(string $email, string $password)
{
    $emailStatus = (int) DB::getRow("SELECT COUNT(*) as count FROM users WHERE email = ? LIMIT 1", [$email])["count"];

    if ($emailStatus !== 0 || strlen($email) === 0) {
        echo json_encode(["error" => 1, "message" => "email not allowed"]);
        return;
    }

    if (strlen($password) === 0) {
        echo json_encode(["error" => 1, "message" => "password not allowed"]);
        return;
    }

    $password_hash = md5($password + "salt");

    DB::sql("INSERT INTO users (`email`, `passwordHash`) VALUES (?,?)", [$email, $password_hash]);

    echo json_encode(["status" => "user is created"]);

    return 1;
}

function restorePassword(string $email, string $password)
{
    $emailStatus = (int) DB::getRow("SELECT COUNT(*) as count FROM users WHERE email = ? LIMIT 1", [$email])["count"];

    if ($emailStatus === 0 || strlen($email) === 0) {
        echo json_encode(["error" => 1, "message" => "email not found"]);
        return;
    }

    if (strlen($password) === 0) {
        echo json_encode(["error" => 1, "message" => "password not allowed"]);
        return;
    }

    $password_hash = md5($password + "salt");

    DB::sql("UPDATE users SET passwordHash = ? WHERE email = ?", [$password_hash, $email]);

    echo json_encode(["status" => "ok"]);

    return 1;
}

function getArticlePageCount($max, $search = "")
{
    $articleCount = (int) DB::getRow("SELECT COUNT(*) as count FROM articles WHERE text LIKE CONCAT('%', ?, '%')", [$search])["count"];

    $articleCount = ceil($articleCount / $max);

    echo json_encode(["pageCount" => $articleCount]);

    return 1;
}

function getArticle($max, $page, $search = "")
{
    $countElement = $max * ($page - 1);

    $articles = DB::getRows("SELECT articles.id as id, title, date, articles.image as image, text, author.name as name, author.image as author_image FROM articles INNER JOIN author ON author.id = author_id WHERE text LIKE CONCAT('%', ?, '%') LIMIT $countElement, $max;", [$search]);

    echo json_encode($articles);

    return 1;
}
