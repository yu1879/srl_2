<?php
require_once('./database.php');
session_start();
date_default_timezone_set("Asia/Taipei");

if (!$_SESSION['account']) {
    header("Location: ./login.php");
    exit();
}

$school_id = $_SESSION['school_id'] ?? $_POST['school_id'] ?? null;
echo "current school: $school_id<br>";
$user_level = $_SESSION['user_level'] ?? null;

$project = $_POST['project'] ?? null;
$year = $_POST['year'] ?? null;
$month = $_POST['month'] ?? null;
$state = $_POST['state'] ?? '0';

$referer = $_SERVER['HTTP_REFERER'] ?? null;
$readonly = $user_level !== '1';

$connect = connect_sql();
$school_data = select($connect, 'school_detail', 'SchoolType', "where sch_id='$school_id'");
$school_type = $school_data[0]['SchoolType'] ?? null;
$data = select(
    $connect,
    'demo_counseling',
    '*',
    "where project='$project' and year='$year' and month='$month'" . (is_null($school_id) ? '' : " and unit='$school_id'") . " order by version desc"
);

$count = $data[0]['times'] ?? null;
$time = $data[0]['times1'] ?? null;
$expert = $data[0]['specialist'] ?? null;
$record = $data[0]['record'] ?? null;

$method = $_POST['method'] ?? null;

if (!$readonly) {
    $count = $_POST['count'] ?? $count;
    $time = $_POST['time'] ?? $time ?? date('Y-m-d H:i:s');
    $expert = $_POST['expert'] ?? $expert;

    if ($method === 'upload' || $state === '-1') {
        $update_time = date('Y-m-d H:i:s');
        $version = ($data[0]['version'] ?? 0) + 1;
        $directory = "./upload/demo_counseling/$year-$month/$school_id";

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $record_file = $_FILES['record'] ?? null;
        $record = upload_file($record_file, "$directory/record_$version");

        insert(
            $connect,
            'demo_counseling',
            "'$project', '$year', '$month', '$school_type', '$state', '$count', '$time', '$expert', '$record', '', '$school_id', '$update_time', '$version'",
            'project, year, month, SchoolType, state, times, times1, specialist, record, record1, unit, updatetime, version'
        );

        if ($state != -1) {
            $_SESSION['message'] = '上傳成功';
        }

        header('Location: ./y10.php#top');
        exit();
    } else if ($method === 'save') {
        print_r($_POST);
        print_r($_FILES);
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
                <h1 class="text-2xl">當月入校輔導紀錄</h1>
                <h3 class="text-xl text-error">* 請填寫''當月份''數值，非累計值 *</h3>
            </div>

            <form class="form-control" name="form" method="post" enctype="multipart/form-data" action="./y02.php#top">
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
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">辦理場次：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <input class="input input-sm input-bordered w-full" type="number" name="count" required value="<?= $count ?>" <?php if ($readonly) echo 'disabled' ?> />
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">辦理時間(時段)：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <input class="input input-sm input-bordered w-full" type="datetime-local" name="time" required value="<?= date('Y-m-d H:i:s', strtotime($time ?? date('Y-m-d H:i:s'))) ?>" <?php if ($readonly) echo 'disabled' ?> />
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">入校輔導專家：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <input class="input input-sm input-bordered w-full" type="text" name="expert" required value="<?= $expert ?>" <?php if ($readonly) echo 'disabled' ?> />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">入校輔導紀錄表(檔案)：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <?php if (!$readonly) { ?>
                            <input id="record" class="file-input file-input-bordered file-input-sm w-full" type="file" name="record" required accept=".pdf" onChange="set_text('record')">
                            <p id="record_text" class="text-base opacity-50"></p>
                        <?php } ?>
                        <?php if ($record) {
                            $file_path = explode('/', $record) ?>
                            <a class="btn btn-sm btn-outline" href="<?= $record ?>" download><?= end($file_path) ?></a>
                        <?php } ?>
                        <a class="btn btn-sm btn-outline" href="https://fidssl.ntue.edu.tw/srl_2/download/112%e5%b9%b45G%e6%99%ba%e6%85%a7%e5%ad%b8%e7%bf%92%e6%8e%a8%e5%8b%95%e8%a8%88%e7%95%ab-%e5%85%a5%e6%a0%a1%e8%bc%94%e5%b0%8e%e8%a1%a8%e6%a0%bc%e8%b3%87%e6%96%99(For%e5%ad%b8%e6%a0%a1).pdf" target="_blank" download>入校輔導紀錄表</a>
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

    <script>
        function click_upload(id) {
            document.getElementById(id).click();
        }

        function set_text(id) {
            value = document.getElementById(id).value.replace(/\\/g, '/').split('/').pop();
            if (value == '') {
                return;
            }
            document.getElementById(`${id}_text`).innerText = value;
        }
    </script>
</body>

</html>