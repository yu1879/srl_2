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
  'result',
  '*',
  "where project='$project' and year='$year' and month='$month'" . (is_null($school_id) ? '' : " and unit='$school_id'") . " order by version desc"
);

$activ = $data[0]['activ'] ?? null;
$video = $data[0]['video'] ?? null;
$video1 = $data[0]['video1'] ?? null;
$report = $data[0]['report'] ?? null;

$method = $_POST['method'] ?? null;

if (!$readonly) {
    if ($method === 'upload' || $state === '-1') {
        date_default_timezone_set("Asia/Taipei");
        $update_time = date('Y-m-d H:i:s');
        $version = ($data[0]['version'] ?? 0) + 1;
        $directory = "./upload/result/$year-$month/$school_id";

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $activ_file = $_FILES['activ'] ?? null;
        $activ = upload_file($activ_file, "$directory/activ_$version");
        $video_file = $_FILES['video'] ?? null;
        $video = upload_file($video_file, "$directory/video_$version");
        $report_file = $_FILES['report'] ?? null;
        $report = upload_file($report_file, "$directory/report_$version");

        insert(
            $connect,
            'result',
            "'$project', '$year', '$month', '$school_type', '$state', '$activ', '$video', '$video1', '$report', '$school_id', '$update_time', '$version'",
            'project, year, month, SchoolType, state, activ, video, video1, report, unit, updatetime, version'
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
        <h1 class="text-2xl">成果展現與推廣活動</h1>
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
              <span class="label-text flex-1">當月校外推廣活動：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="activ" class="file-input file-input-bordered file-input-sm w-full" type="file" name="activ" required accept=".pdf" onChange="set_text('activ')">
              <p id="activ_text" class="text-base opacity-50"></p>
            <?php } ?>
            <?php if ($activ) {
              $file_path = explode('/', $activ) ?>
              <a class="btn btn-sm btn-outline" href="<?= $activ ?>" download><?= end($file_path) ?></a>
            <?php } ?>
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">成果影片：每年1支成果影片3-5分鐘</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="video" class="file-input file-input-bordered file-input-sm w-full" type="file" name="video" onChange="set_text('video')">
              <p id="video_text" class="text-base opacity-50"></p>
              <input class="input input-sm input-bordered w-full" type="text" name="video1" placeholder="輸入影片連結" value="<?= $video1 ?>" <?php if ($readonly) echo 'disabled' ?> />
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
              <span class="label-text flex-1">活動文宣：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="report" class="file-input file-input-bordered file-input-sm w-full" type="file" name="report" required accept=".pdf" onChange="set_text('report')">
              <p id="report_text" class="text-base opacity-50"></p>
            <?php } ?>
            <?php if ($report) {
              $file_path = explode('/', $report) ?>
              <a class="btn btn-sm btn-outline" href="<?= $report ?>" download><?= end($file_path) ?></a>
            <?php } ?>
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