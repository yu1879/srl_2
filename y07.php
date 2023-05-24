<?php
require_once('./database.php');
session_start();
date_default_timezone_set("Asia/Taipei");

if (!$_SESSION['account']) {
    header("Location: ./login.php");
    exit();
}

$unit = $_SESSION['unit'];
$account = $_SESSION['account'];
$user_level = $_SESSION['user_level'] ?? null;

$project = $_POST['project'] ?? null;
$year = $_POST['year'] ?? null;
$month = $_POST['month'] ?? null;
$state = $_POST['state'] ?? '0';

$referer = $_SERVER['HTTP_REFERER'] ?? null;
$readonly = $user_level !== '2';

$effect = $_POST['計畫效益與亮點'] ?? null;
$difficulty = $_POST['遭遇困難與應對對策應對對策'] ?? null;
$after = $_POST['後續精進措施'] ?? null;
$reason = $_POST['未達目標原因說明'] ?? null;

$connect = connect_sql();
$data = select($connect, 'PU023', '*', "where year='$year' and season='$month' and Account='$account' and unit='$unit' order by version desc"); // add projectid
if (is_null($effect)) {
    $effect = $data[0]['project_point'] ?? null;
    $difficulty = $data[0]['question'] ?? null;
    $after = $data[0]['next_method'] ?? null;
    $reason = $data[0]['solve_method'] ?? null;
}

$method = $_POST['method'] ?? null;

if (!$readonly) {
    if ($method === 'upload' || $state === '-1') {
        $upload_time = $data[0]['upload_time'] ?? date('Y-m-d H:i:s');
        $update_time = date('Y-m-d H:i:s');
        $version = ($data[0]['version'] ?? 0) + 1;

        insert(
            $connect,
            'PU023',
            "'$upload_time', '$year', '$month', '$account','$project', '$unit', '$effect', '$difficulty', '$after', '$reason', '$update_time', '$version', '$state'",
            "uploadtime, year, season, Account, projectid, unit, project_point, question, next_method, solve_method, updatetime, version, state"
        );

        if ($state != -1) {
            $_SESSION['message'] = '上傳成功';
        }

        header('Location: ./y11.php#top');
        exit();
    } else if ($method === 'save') {
        print_r($_POST);
    }
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
    <div class="max-w-5xl mx-auto lg:pb-4">
        <?php require('./navbar.php') ?>

        <div id="top" class="p-4 max-w-3xl mx-auto bg-base-200 lg:rounded-xl">
            <h1 class="text-2xl">計畫效益說明</h1>

            <form class="form-control" name="form" method="post" action="./y07.php#top">
                <input type="hidden" name="project" value="<?= $project ?>">
                <input type="hidden" name="year" value="<?= $year ?>">
                <input type="hidden" name="month" value="<?= $month ?>">

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-4">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4 sm:col-span-2">
                        <p class="text-base">計畫名稱：</p>
                        <p class="text-lg"><?= $project ?></p>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <p class="text-base">年度：</p>
                        <p class="text-lg"><?= $year ?></p>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <p class="text-base">填報月份：</p>
                        <p class="text-lg"><?= $month ?>月</p>
                    </div>
                </div>
                <div class="mt-2 flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                    <label class="label gap-2">
                        <span class="label-text flex-1">計畫效益與亮點：</span>
                        <span class="label-text-alt text-error">必填欄位</span>
                    </label>
                    <textarea class="textarea textarea-bordered w-full rounded-lg text-lg" rows="5" name="計畫效益與亮點"><?= $effect ?></textarea>
                </div>
                <div class="mt-2 flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                    <label class="label gap-2">
                        <span class="label-text flex-1">遭遇困難與應對對策應對對策：</span>
                        <span class="label-text-alt text-error">必填欄位</span>
                    </label>
                    <textarea class="textarea textarea-bordered w-full rounded-lg text-lg" rows="5" name="遭遇困難與應對對策應對對策"><?= $difficulty ?></textarea>
                </div>
                <div class="mt-2 flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                    <label class="label gap-2">
                        <span class="label-text flex-1">後續精進措施：</span>
                        <span class="label-text-alt text-error">必填欄位</span>
                    </label>
                    <textarea class="textarea textarea-bordered w-full rounded-lg text-lg" rows="5" name="後續精進措施"><?= $after ?></textarea>
                </div>
                <div class="mt-2 flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                    <label class="label gap-2">
                        <span class="label-text flex-1">未達目標原因說明：</span>
                        <span class="label-text-alt text-error">必填欄位</span>
                    </label>
                    <textarea class="textarea textarea-bordered w-full rounded-lg text-lg" rows="5" name="未達目標原因說明"><?= $reason ?></textarea>
                </div>

                <?php if (!$readonly) { ?>
                    <div class="flex gap-2 justify-center p-4">
                        <button class="btn btn-sm w-24" name="method" value="upload">上傳</button>
                        <!-- <button class="btn btn-sm w-24" name="method" value="save">暫存</button> -->
                        <a class="btn btn-sm w-24" href="/y11.php">返回</a>
                    </div>
                <?php } ?>
            </form>

            <?php if ($readonly) { ?>
                <div class="flex gap-2 justify-center p-4">
                    <a class="btn btn-sm w-24" href="/y08.php">返回</a>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>