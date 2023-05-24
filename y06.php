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

$B1_count = $_POST['B1科技輔助自主學習工作坊總場次'] ?? null;
$B1_teacher = $_POST['B1科技輔助自主學習工作坊教師培訓數'] ?? null;
$_5G_count = $_POST['5G應用之教學與導入自主學習模式總場次'] ?? null;
$_5G_teacher = $_POST['5G應用之教學與導入自主學習模式教師培訓數'] ?? null;
$other_count = $_POST['其他進階研習總場次'] ?? null;
$other_teacher = $_POST['其他進階研習教師培訓數'] ?? null;
$cross_count = $_POST['跨校公開授課總場次'] ?? null;
$cross_teacher = $_POST['跨校公開授課教師數'] ?? null;
$progress = $_POST['執行進度與成果'] ?? null;
$link = $_POST['雲端連結'] ?? null;

$connect = connect_sql();
$data = select($connect, 'PU021', '*', "where year='$year' and month='$month' and Account='$account' and unit='$unit' order by version desc"); // add projectid
if (is_null($B1_count)) {
    $B1_count = $data[0]['cross1_sum'] ?? null;
    $B1_teacher = $data[0]['cross1_people'] ?? null;
    $_5G_count = $data[0]['cross1_teacher'] ?? null;
    $_5G_teacher = $data[0]['cross2_sum'] ?? null;
    $other_count = $data[0]['cross2_people'] ?? null;
    $other_teacher = $data[0]['cross2_teacher'] ?? null;
    $cross_count = $data[0]['cross3_teacher'] ?? null;
    $cross_teacher = $data[0]['cross4_teacher'] ?? null;
    $progress = $data[0]['cross5_sum'] ?? null;
    $link = $data[0]['cross5_people'] ?? null;
}

$method = $_POST['method'] ?? null;

if (!$readonly) {
    if ($method === 'upload' || $state === '-1') {
        $upload_time = $data[0]['uploadtime'] ?? date('Y-m-d H:i:s');
        $update_time = date('Y-m-d H:i:s');
        $version = ($data[0]['version'] ?? 0) + 1;

        insert(
            $connect,
            'PU021',
            "'$upload_time', '$year', '$month', '$account', '$project', '$unit', '$B1_count', '$B1_teacher', '$_5G_count', '$_5G_teacher', '$other_count', '$other_teacher', '$cross_count', '$cross_teacher', '$progress', '$link', '$update_time', '$version', '$state'",
            "uploadtime, year, month, Account, projectid, unit, cross1_sum, cross1_people, cross1_teacher, cross2_sum, cross2_people, cross2_teacher, cross3_teacher, cross4_teacher, cross5_sum, cross5_people, updatetime, version, state"
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
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h1 class="text-2xl">縣市教育局處每月月報</h1>
                <h3 class="text-xl text-error">* 請填寫''當月份''數值，非累計值 *</h3>
            </div>

            <form class="form-control" name="form" method="post" action="./y06.php#top">
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">B1科技輔助自主學習工作坊辦理場次：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered w-full" type="number" name="B1科技輔助自主學習工作坊總場次" required value="<?= $B1_count ?>" />
                            <p class="text-base">場次數</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">B1科技輔助自主學習工作坊培訓教師數：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered w-full" type="number" name="B1科技輔助自主學習工作坊教師培訓數" required value="<?= $B1_teacher ?>" />
                            <p class="text-base">教師數</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">5G應用之教學與導入自主學習模式辦理場次：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered w-full" type="number" name="5G應用之教學與導入自主學習模式總場次" required value="<?= $_5G_count ?>" />
                            <p class="text-base">場次數</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">5G應用之教學與導入自主學習模式培訓教師數：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered w-full" type="number" name="5G應用之教學與導入自主學習模式教師培訓數" required value="<?= $_5G_teacher ?>" />
                            <p class="text-base">教師數</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">其他進階研習辦理場次：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered w-full" type="number" name="其他進階研習總場次" required value="<?= $other_count ?>" />
                            <p class="text-base">場次數</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">其他進階研習培訓教師數：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered w-full" type="number" name="其他進階研習教師培訓數" required value="<?= $other_teacher ?>" />
                            <p class="text-base">教師數</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">跨校公開授課辦理場次：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered w-full" type="number" name="跨校公開授課總場次" required value="<?= $cross_count ?>" />
                            <p class="text-base">場次數</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">跨校公開授課參加教師數：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered w-full" type="number" name="跨校公開授課教師數" required value="<?= $cross_teacher ?>" />
                            <p class="text-base">教師數</p>
                        </div>
                    </div>
                </div>
                <div class="mt-2 flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                    <label class="label gap-2">
                        <span class="label-text flex-1">執行進度與成果：</span>
                        <span class="label-text-alt text-error">必填欄位</span>
                    </label>
                    <textarea class="textarea textarea-bordered w-full rounded-lg text-lg" rows="5" name="執行進度與成果" required><?= $progress ?></textarea>
                </div>
                <div class="mt-2 flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                    <label class="label gap-2">
                        <span class="label-text flex-1">上傳照片或影片(請提供雲端連結)：</span>
                        <span class="label-text-alt text-error">必填欄位</span>
                    </label>
                    <input class="input input-sm input-bordered w-full" type="text" name="雲端連結" required value="<?= $link ?>" />
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