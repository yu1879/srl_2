<?php
require_once('./database.php');
session_start();

function redirect($destination)
{
  header("Location: {$destination}");
  exit();
}

if (!$_SESSION['account']) {
  header("Location: ./login.php");
  exit();
}

$account = $_SESSION['account'] ?? null;
$pworigin = $_POST['pworigin'] ?? null;
$pwnew = $_POST['pwnew'] ?? null;

if (is_null($pworigin) || is_null($pwnew)) {
  redirect('./password.php');
}

$connect = connect_sql();
$data = select($connect, 'users', '*', "where account='{$account}' and password='{$pworigin}'");

if (count($data) === 0) {
  $_SESSION['message'] = '原密碼不正確';
  redirect('./password.php');
} else {
  update($connect, 'users', "password='{$pwnew}'", "where account='{$account}'");
  redirect('./logout.php');
}
