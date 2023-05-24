<?php
session_start();

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
    <div class="fixed inset-0 flex flex-col items-center bg-base-200 overflow-auto">
        <div class="m-10 flex-1 flex flex-col items-center justify-center">
            <div class="rounded-xl shadow-lg bg-base-100 w-80">
                <img src="./images/banner.png" class="max-w-xs rounded-t-xl" />
                <div class="p-8">
                    <h1 class="text-3xl text-center">計畫管考系統</h1>
                    <form class="form-control mt-8" method="post" action="./check_login.php">
                        <label class="label">
                            <span class="label-text">帳號：</span>
                        </label>
                        <input type="text" id="username" name="username" required class="input input-bordered w-full max-w-xs">
                        <label class="label">
                            <span class="label-text">密碼：</span>
                        </label>
                        <input type="password" id="password" name="password" required class="input input-bordered w-full max-w-xs">
                        <button type="submit" class="btn btn-block mt-8">登入</button>
                    </form>
                </div>
            </div>
            <div class="rounded-xl shadow-lg bg-base-100 p-8 mt-4 w-80">
                <h1 class="text-2xl text-center">䏈絡人</h1>
                <p class="text-lg mt-4">北區輔導團</p>
                <p class="text-lg">喻聖為</p>
                <p class="text-base">2019srlearning@mail.ntue.edu.tw</p>
            </div>
        </div>
    </div>
</body>

</html>