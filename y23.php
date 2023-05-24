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
  'around',
  '*',
  "where project='$project' and year='$year' and month='$month'" . (is_null($school_id) ? '' : " and unit='$school_id'") . " order by version desc"
);

$text = $data[0]['text'] ?? null;
$report = $data[0]['report'] ?? null;
$manual = $data[0]['manual'] ?? null;
$text_1 = $data[0]['text_1'] ?? null;

$method = $_POST['method'] ?? null;

if (!$readonly) {
  $text = $_POST['text'] ?? $text;

  if ($method === 'upload' || $state === '-1') {
    date_default_timezone_set("Asia/Taipei");
    $update_time = date('Y-m-d H:i:s');
    $version = ($data[0]['version'] ?? 0) + 1;
    $directory = "./upload/around/$year-$month/$school_id";

    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }

    $report_file = $_FILES['report'] ?? null;
    $report = upload_file($report_file, "$directory/report_$version");
    $manual_file = $_FILES['manual'] ?? null;
    $manual = upload_file($manual_file, "$directory/manual_$version");
    $text_1_file = $_FILES['text_1'] ?? null;
    $text_1 = upload_file($text_1_file, "$directory/text_1_$version");

    insert(
      $connect,
      'around',
      "'$project', '$year', '$month', '$school_type', '$state', '$text', '$report', '$manual', '$text_1', '$school_id', '$update_time', '$version'",
      'project, year, month, SchoolType, state, text, report, manual, text_1, unit, updatetime, version'
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
        <h1 class="text-2xl">主播端環境建置</h1>
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
              <span class="label-text flex-1">主播端環境建置成果照片加200字以上軟硬體設備文字說明：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <input class="input input-sm input-bordered w-full" type="number" name="text" required value="<?= $text ?>" <?php if ($readonly) echo 'disabled' ?> />
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">環境建置說明與測試報告：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="class" class="file-input file-input-bordered file-input-sm w-full" type="file" name="report" required accept=".pdf" onChange="set_text('report')">
              <p id="report_text" class="text-base opacity-50"></p>
            <?php } ?>
            <?php if ($report) {
              $file_path = explode('/', $report) ?>
              <a class="btn btn-sm btn-outline" href="<?= $report ?>" download><?= end($file_path) ?></a>
            <?php } ?>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">XR數位共學中心模式工作流程、分工說明書與操作手冊：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="manual" class="file-input file-input-bordered file-input-sm w-full" type="file" name="manual" required accept=".pdf" onChange="set_text('manual')">
              <p id="manual_text" class="text-base opacity-50"></p>
            <?php } ?>
            <?php if ($manual) {
              $file_path = explode('/', $manual) ?>
              <a class="btn btn-sm btn-outline" href="<?= $manual ?>" download><?= end($file_path) ?></a>
            <?php } ?>
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">XR數位共學中心長期營運規劃書：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="text_1" class="file-input file-input-bordered file-input-sm w-full" type="file" name="text_1" required accept=".pdf" onChange="set_text('text_1')">
              <p id="text_1_text" class="text-base opacity-50"></p>
            <?php } ?>
            <?php if ($text_1) {
              $file_path = explode('/', $text_1) ?>
              <a class="btn btn-sm btn-outline" href="<?= $text_1 ?>" download><?= end($file_path) ?></a>
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