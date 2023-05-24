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
  'lesson',
  '*',
  "where project='$project' and year='$year' and month='$month'" . (is_null($school_id) ? '' : " and unit='$school_id'") . " order by version desc"
);

$subjects = $data[0]['subjects'] ?? null;
$student = $data[0]['student'] ?? null;
$students = $data[0]['students'] ?? null;
$time = $data[0]['time'] ?? null;
$result = $data[0]['result'] ?? null;
$result_1 = $data[0]['result_1'] ?? null;
$video = $data[0]['video'] ?? null;
$video_1 = $data[0]['video_1'] ?? null;
$sheet = $data[0]['sheet'] ?? null;
$date = $data[0]['date'] ?? null;

$method = $_POST['method'] ?? null;

if (!$readonly) {
    $subjects = $_POST['subjects'] ?? null;
    $student = $_POST['student'] ?? null;
    $students = $_POST['students'] ?? null;
    $time = $_POST['time'] ?? null;
    $date = $_POST['date'] ?? null;

    if ($method === 'upload' || $state === '-1') {
        date_default_timezone_set("Asia/Taipei");
        $update_time = date('Y-m-d H:i:s');
        $version = ($data[0]['version'] ?? 0) + 1;
        $directory = "./upload/lesson/$year-$month/$school_id";

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $result_file = $_FILES['result'] ?? null;
        $result = upload_file($result_file, "$directory/result_$version");
        $video_file = $_FILES['video'] ?? null;
        $video = upload_file($video_file, "$directory/video_$version");
        $sheet_file = $_FILES['sheet'] ?? null;
        $sheet = upload_file($sheet_file, "$directory/sheet_$version");
        
        insert(
            $connect,
            'lesson',
            "'$project', '$year', '$month', '$school_type', '$state', '$subjects', '$student', '$students', '$time', '$result', '$result_1', '$video', '$video_1', '$sheet', '$data', '$school_id', '$update_time', '$version'",
            'project, year, month, SchoolType, state, times, subjects, student, students, time, result, result_1, video, video_1, sheet, sheet, date, upload, unit, updatetime, version'
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
        <h1 class="text-2xl">共學課程實施</h1>
        <h3 class="text-xl text-error">* 請填寫''當月份''數值，非累計值 *</h3>
      </div>

      <form class="form-control" name="form" method="post" enctype="multipart/form-data" action="./y14.php#top">
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
              <span class="label-text flex-1">學科：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <input class="input input-sm input-bordered w-full" type="text" name="subjects" required value="<?= $subjects ?>" <?php if ($readonly) echo 'disabled' ?> />
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">收播學校數：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <input class="input input-sm input-bordered w-full" type="number" name="student" required value="<?= $student ?>" <?php if ($readonly) echo 'disabled' ?> />
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">收播學生人數：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <input class="input input-sm input-bordered w-full" type="number" name="students" required value="<?= $students ?>" <?php if ($readonly) echo 'disabled' ?> />
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">實施時間：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <input class="input input-sm input-bordered w-full" type="text" name="time" required value="<?= $time ?>" <?php if ($readonly) echo 'disabled' ?> />
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">主播端成果照片加200字以上文字說明：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="result" class="file-input file-input-bordered file-input-sm w-full" type="file" name="result" required accept=".pdf" onChange="set_text('result')">
              <p id="result_text" class="text-base opacity-50"></p>
            <?php } ?>
            <?php if ($result) {
              $file_path = explode('/', $result) ?>
              <a class="btn btn-sm btn-outline" href="<?= $result ?>" download><?= end($file_path) ?></a>
            <?php } ?>
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">主播端直播教學影片：</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="video" class="file-input file-input-bordered file-input-sm w-full" type="file" name="video" onChange="set_text('video')">
              <p id="video_text" class="text-base opacity-50"></p>
              <input class="input input-sm input-bordered w-full" type="text" name="video_1" placeholder="輸入影片連結" value="<?= $video_1 ?>" <?php if ($readonly) echo 'disabled' ?> />
            <?php } ?>
            <?php if ($video) {
              $file_path = explode('/', $video) ?>
              <a class="btn btn-sm btn-outline" href="<?= $video ?>" download><?= end($file_path) ?></a>
            <?php } ?>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">共學課程教案(含學習單)：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="sheet" class="file-input file-input-bordered file-input-sm w-full" type="file" name="sheet" required accept=".pdf" onChange="set_text('sheet')">
              <p id="sheet_text" class="text-base opacity-50"></p>
            <?php } ?>
            <?php if ($sheet) {
              $file_path = explode('/', $sheet) ?>
              <a class="btn btn-sm btn-outline" href="<?= $sheet ?>" download><?= end($file_path) ?></a>
            <?php } ?>
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <label class="label gap-2">
                            <span class="label-text flex-1">共學課程開課實施日期
(時段)：</span>
                            <span class="label-text-alt text-error">必填欄位</span>
                        </label>
                        <input class="input input-sm input-bordered w-full" type="datetime-local" name="times1" required value="<?= date('Y-m-d H:i', strtotime($times1 ?? date('Y-m-d H:i'))) ?>" <?php if ($readonly) echo 'disabled' ?> />
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