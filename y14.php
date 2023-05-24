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

$project = $_POST['project'] ?? null;
$year = $_POST['year'] ?? null;
$month = $_POST['month'] ?? null;
$state = $_POST['state'] ?? '0';

$referer = $_SERVER['HTTP_REFERER'] ?? null;
$readonly = $user_level !== '1';

$connect = connect_sql();
$data = select(
  $connect,
  'demo_school',
  '*',
  "where project='$project' and year='$year' and month='$month'" . (is_null($school_id) ? '' : " and unit='$school_id'") . " order by version desc"
);

$teacher = $data[0]['teacher'] ?? null;
$times = $data[0]['times'] ?? null;
$times1 = $data[0]['times1'] ?? null;
$grade = $data[0]['grade'] ?? null;
$unit = $data[0]['field'] ?? null;
$teacher_count = $data[0]['teachers'] ?? null;
$cross_count = $data[0]['public_teachers'] ?? null;
$teach_project = $data[0]['file'] ?? null;
$photo = $data[0]['photo'] ?? null;
$record = $data[0]['record_1'] ?? null;
$video = $data[0]['video'] ?? null;
$videos = $data[0]['videos'] ?? null;
$content = $data[0]['text'] ?? null;

$method = $_POST['method'] ?? null;

if (!$readonly) {
  $teacher = $_POST['teacher'] ?? $teacher;
  $times = $_POST['times'] ?? $times;
  $times1 = $_POST['times1'] ?? $times1 ?? date('Y-m-d H:i:s');
  $grade = $_POST['grade'] ?? $grade;
  $unit = $_POST['unit'] ?? $unit;
  $teacher_count = $_POST['teacher_count'] ?? $teacher_count;
  $cross_count = $_POST['cross_count'] ?? $cross_count;
  $videos = $_POST['videos'] ?? $videos;
  $content = $_POST['content'] ?? $content;

  if ($method === 'upload' || $state === '-1') {
    date_default_timezone_set("Asia/Taipei");
    $update_time = date('Y-m-d H:i:s');
    $version = ($data[0]['version'] ?? 0) + 1;
    $directory = "./upload/demo_school/$year-$month/$school_id";

    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }

    $teach_project_file = $_FILES['teach_project'] ?? null;
    $teach_project = upload_file($teach_project_file, "$directory/file_$version");
    $photo_file = $_FILES['photo'] ?? null;
    $photo = upload_file($photo_file, "$directory/photo_$version");
    $record_file = $_FILES['record'] ?? null;
    $record = upload_file($record_file, "$directory/record_$version");
    $video_file = $_FILES['video'] ?? null;
    $video = upload_file($video_file, "$directory/video_$version");

    insert(
      $connect,
      'demo_school',
      "'$project', '$year', '$month', '$school_type', '$state', '$times', '$times1', '$teacher', '$grade', '$unit', '$teacher_count', '$teach_project', '$photo', '$record', '$video', '$videos', '$content', '$cross_count', '$school_id', '$update_time', '$version'",
      'project, year, month, SchoolType, state, times, times1, teacher, grade, field, teachers, file, photo, record_1, video, videos, text, public_teachers, unit, updatetime, version'
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
        <h1 class="text-2xl">當月開放教室紀錄</h1>
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
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-2">
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">授課教師：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <input class="input input-sm input-bordered w-full" type="text" name="teacher" required value="<?= $teacher ?>" <?php if ($readonly) echo 'disabled' ?> />
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">辦理場次：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <input class="input input-sm input-bordered w-full" type="number" name="times" required value="<?= $times ?>" <?php if ($readonly) echo 'disabled' ?> />
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">辦理時間(時段)：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <input class="input input-sm input-bordered w-full" type="datetime-local" name="times1" required value="<?= date('Y-m-d H:i', strtotime($times1 ?? date('Y-m-d H:i'))) ?>" <?php if ($readonly) echo 'disabled' ?> />
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">年級：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <input class="input input-sm input-bordered w-full" type="text" name="grade" required value="<?= $grade ?>" <?php if ($readonly) echo 'disabled' ?> />
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">領域/單元：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <input class="input input-sm input-bordered w-full" type="text" name="unit" required value="<?= $unit ?>" <?php if ($readonly) echo 'disabled' ?> />
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">實到教師數：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <input class="input input-sm input-bordered w-full" type="number" name="teacher_count" required value="<?= $teacher_count ?>" <?php if ($readonly) echo 'disabled' ?> />
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">參與跨校或跨縣市公開授課教師數：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <input class="input input-sm input-bordered w-full" type="number" name="cross_count" required value="<?= $cross_count ?>" <?php if ($readonly) echo 'disabled' ?> />
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">教材教案：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="teach_project" class="file-input file-input-bordered file-input-sm w-full" type="file" name="teach_project" required accept=".pdf" onChange="set_text('teach_project')">
              <p id="teach_project_text" class="text-base opacity-50"></p>
            <?php } ?>
            <?php if ($teach_project) {
              $file_path = explode('/', $teach_project) ?>
              <a class="btn btn-sm btn-outline" href="<?= $teach_project ?>" download><?= end($file_path) ?></a>
            <?php } ?>
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">成果照片：</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="photo" class="file-input file-input-bordered file-input-sm w-full" type="file" name="photo" required accept=".png, .jpg" onChange="set_text('photo')">
              <p id="photo_text" class="text-base opacity-50"></p>
            <?php } ?>
            <?php if ($photo) {
              $file_path = explode('/', $photo) ?>
              <a class="btn btn-sm btn-outline" href="<?= $photo ?>" download><?= end($file_path) ?></a>
            <?php } ?>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">紀錄表：</span>
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
            <a class="btn btn-sm btn-outline" href="https://fidssl.ntue.edu.tw/srl_2/download/5G%e6%99%ba%e6%85%a7%e5%ad%b8%e7%bf%92%e6%8e%a8%e5%8b%95%e8%a8%88%e7%95%ab%e9%96%8b%e6%94%be%e6%95%99%e5%ae%a4%e7%b4%80%e9%8c%84%e8%a1%a8.pdf" target="_blank" download>開放教室紀錄表</a>
          </div>
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">影片檔案上傳：</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="video" class="file-input file-input-bordered file-input-sm w-full" type="file" name="video" onChange="set_text('video')">
              <p id="video_text" class="text-base opacity-50"></p>
              <input class="input input-sm input-bordered w-full" type="text" name="videos" placeholder="輸入影片連結" value="<?= $videos ?>" <?php if ($readonly) echo 'disabled' ?> />
            <?php } ?>
            <?php if ($video) {
              $file_path = explode('/', $video) ?>
              <a class="btn btn-sm btn-outline" href="<?= $video ?>" download><?= end($file_path) ?></a>
            <?php } ?>
          </div>
        </div>
        <div class="mt-2 flex flex-col gap-2 bg-base-100 rounded-lg p-4">
          <label class="label gap-2">
            <span class="label-text flex-1">文字說明(200字以上)：</span>
            <!-- <span class="label-text-alt text-error">必填欄位</span> -->
          </label>
          <textarea class="textarea textarea-bordered w-full rounded-lg text-lg" rows="5" name="content" minlength="200" required <?php if ($readonly) echo 'disabled' ?>><?= $content ?></textarea>
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