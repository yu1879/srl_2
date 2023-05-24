<?php
require_once('./database.php');
session_start();

function redirect($destination)
{
    header("Location: {$destination}");
    exit();
}

$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;

if (is_null($username) || is_null($password)) {
    redirect('./login.php');
}

$connect = connect_sql();
$data = select($connect, 'users', '*', "where account='{$username}'");

if (count($data) === 0) {
    $_SESSION['message'] = '帳號不存在';
    redirect('./login.php');
} else if ($password !== $data[0]['password']) {
    $_SESSION['message'] = '密碼錯誤';
    redirect('./login.php');
} else {
    $user_level = $data[0]['UserLevel'];

    $_SESSION['account'] = $username;
    $_SESSION['unit'] = $data[0]['Unit'];
    $_SESSION['user'] = $data[0]['User'];
    $_SESSION['user_level'] = $user_level;

    // 學校
    if ($user_level === '1') {
        $_SESSION['school_id'] = $data[0]['sch_id'];
        $data = select($connect, 'school_detail', '*', "where sch_id={$data[0]['sch_id']}");
        $_SESSION['school_type'] = $data[0]['SchoolType'];
        redirect('./y10.php');
    }
    // 縣市
    else if ($user_level === '2') {
        redirect('./y11.php');
    }
}
