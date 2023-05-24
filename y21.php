<?php
require_once('./database.php');
session_start();

if (!$_SESSION['account']) {
    header("Location: ./login.php");
    exit();
}

$school_id = $_SESSION['school_id'] ?? null;
$school_type = $_SESSION['school_type'] ?? null;
$user_level = $_SESSION['user_level'] ?? null;

// $project = $_POST['project'] ?? null;
// $year = $_POST['year'] ?? null;
// $month = $_POST['month'] ?? null;
$project = $_POST['project'] ?? "112-113年5G新科技學習示範學校計畫";
$year = $_POST['year'] ?? "112";
$month = $_POST['month'] ?? "5";
$state = $_POST['state'] ?? '0';

$referer = $_SERVER['HTTP_REFERER'] ?? null;
// $readonly = $user_level !== '1';
$readonly = false;

$connect = connect_sql();
$data = select(
    $connect,
    'goal_1',
    '*',
    "where project='$project' and year='$year' and month='$month'" . (is_null($school_id) ? '' : " and unit='$school_id'") . " order by version desc"
);

$m1 = $data[0]['m1'] ?? null;
$m4 = $data[0]['m4'] ?? null;
$m5 = $data[0]['m5'] ?? null;
$time = $data[0]['time'] ?? null;
$school = $data[0]['school'] ?? null;
$subject = $data[0]['subject'] ?? null;
$peoplee = $data[0]['peoplee'] ?? null;
$asset = $data[0]['asset'] ?? null;
$public = $data[0]['public'] ?? null;
$counseling = $data[0]['counseling'] ?? null;
$activ = $data[0]['activ'] ?? null;
$school_type = $data[0]['SchoolType'] ?? $school_type;

$method = $_POST['method'] ?? null;

if (!$readonly) {
    $m1 = $_POST['m1'] ?? $m1;
    $m4 = $_POST['m4'] ?? $m4;
    $m5 = $_POST['m5'] ?? $m5;
    $time = $_POST['time'] ?? $time;
    $school = $_POST['school'] ?? $school;
    $subject = $_POST['subject'] ?? $subject;
    $peoplee = $_POST['peoplee'] ?? $peoplee;
    $asset = $_POST['asset'] ?? $asset;
    $public = $_POST['public'] ?? $public;
    $counseling = $_POST['counseling'] ?? $counseling;
    $activ = $_POST['activ'] ?? $activ;

    if ($method === 'upload' || $state === '-1') {
        date_default_timezone_set("Asia/Taipei");
        $update_time = date('Y-m-d H:i:s');
        $version = ($data[0]['version'] ?? 0) + 1;

        insert(
            $connect,
            'goal',
            "'$project', '$year', '$month', '$school_type', '$state', '$m1', '$m4', '$m5', '$time', '$school', '$subject', '$peoplee', '$asset', '$public', '$counseling', '$activ', '$school_id', '$update_time', '$version'",
            'project, year, month, SchoolType, state, m1, m4, m5, time, school, subject, peoplee, asset, public, counseling, activ, unit, updatetime, version'
        );

        if ($state != -1) {
            $_SESSION['message'] = '上傳成功';
        }

        header('Location: ./y10.php#top');
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
                <h1 class="text-2xl">目標值</h1>
                <h3 class="text-xl text-error">* 請填寫''當月份''數值，非累計值 *</h3>
            </div>

            <form class="form-control" name="form" method="post" action="./y05.php#top">
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
                            <span class="label-text flex-1">M1研習參與人數：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered" type="number" min="0" name="m1" required value="<?= $m1 ?>" <?php if ($readonly) echo 'disabled' ?> />
                            <p class="text-base">研習參與人數</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">M4研習參與人數：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered" type="number" min="0" name="m4" required value="<?= $m4 ?>" <?php if ($readonly) echo 'disabled' ?> />
                            <p class="text-base">研習參與人數</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">M5研習參與人數：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered" type="number" min="0" name="m5" required value="<?= $m5 ?>" <?php if ($readonly) echo 'disabled' ?> />
                            <p class="text-base">研習參與人數</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">年度共學課程開課時間：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered" type="number" min="0" name="time" required value="<?= $time ?>" <?php if ($readonly) echo 'disabled' ?> />
                            <p class="text-base">分鐘</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">年度共學課程累計收播學校數：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered" type="number" min="0" name="school" required value="<?= $school ?>" <?php if ($readonly) echo 'disabled' ?> />
                            <p class="text-base">學校數</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">年度共學課程實施學科數：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered" type="number" min="0" name="subject" required value="<?= $subject ?>" <?php if ($readonly) echo 'disabled' ?> />
                            <p class="text-base">學科數</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">收播端學校學生收播人次：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered" type="number" min="0" name="people" required value="<?= $people ?>" <?php if ($readonly) echo 'disabled' ?> />
                            <p class="text-base">人次</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">成效評估次數：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered" type="number" min="0" name="asset" required value="<?= $asset ?>" <?php if ($readonly) echo 'disabled' ?> />
                            <p class="text-base">次數</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">公開授課辦理場次：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered" type="number" min="0" name="public" required value="<?= $public ?>" <?php if ($readonly) echo 'disabled' ?> />
                            <p class="text-base">場次</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">入校輔導辦理場次：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered" type="number" min="0" name="counseling" required value="<?= $counseling ?>" <?php if ($readonly) echo 'disabled' ?> />
                            <p class="text-base">場次</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">推廣活動辦理場次：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4 items-center">
                            <input class="input input-sm input-bordered" type="number" min="0" name="activ" required value="<?= $activ ?>" <?php if ($readonly) echo 'disabled' ?> />
                            <p class="text-base">場次</p>
                        </div>
                    </div>
                </div>

                <?php if (!$readonly) { ?>
                    <div class="flex gap-2 justify-center p-4">
                        <button class="btn btn-sm w-24" name="method" value="upload">上傳</button>
                        <!-- <button class="btn btn-sm w-24" name="method" value="save">暫存</button> -->
                        <a class="btn btn-sm w-24" href="./y10.php">返回</a>
                    </div>
                <?php } ?>
            </form>

            <?php if ($readonly) { ?>
                <div class="flex gap-2 justify-center p-4">
                    <a class="btn btn-sm w-24" href="./y09.php">返回</a>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>