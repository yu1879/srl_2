<?php
session_start();

if (!$_SESSION['account']) {
  header("Location: ./login.php");
  exit();
}

if (isset($_SESSION['message'])) {
  echo "<script>alert('{$_SESSION['message']}')</script>";
  unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&family=Roboto&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@2.51.5/dist/full.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet" type="text/css" />
  <title>計畫管考系統</title>
  <style>
    html,
    body {
      padding: 0;
      margin: 0;
      font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen,
        Ubuntu, Cantarell, Fira Sans, Droid Sans, Helvetica Neue, Noto Sans TC,
        sans-serif;
    }
  </style>
</head>

<body>
  <div class="fixed inset-0 flex flex-col items-center justify-center bg-base-200">
    <div class="rounded-xl shadow-lg bg-base-100">
      <img src="./images/banner.png" class="max-w-xs rounded-t-xl" />
      <div class="p-8">
        <h1 class="text-2xl text-center">修改密碼</h1>
        <form class="form-control mt-8" method="post" action="./change_password.php">
          <label class="label">
            <span class="label-text">輸入原密碼：</span>
          </label>
          <input type="password" id="pworigin" name="pworigin" required class="input input-bordered w-full max-w-xs">
          <label class="label">
            <span class="label-text">輸入新密碼：</span>
          </label>
          <input type="password" id="pwnew" name="pwnew" required class="input input-bordered w-full max-w-xs">
          <div class="flex justify-center gap-4 mt-8">
            <button type="submit" class="btn" onclick="window.history.back()">取消返回</button>
            <button type="submit" class="btn">變更密碼</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>