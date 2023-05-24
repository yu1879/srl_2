<?php
require_once('./database.php');
session_start();

if (!$_SESSION['account']) {
  header("Location: ./login.php");
  exit();
}

$school_id = $_SESSION['school_id'] ?? $_POST['school_id'] ?? null;
echo "current school: $school_id<br>";
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
  'demo_result',
  '*',
  "where project='$project' and year='$year' and month='$month'" . (is_null($school_id) ? '' : " and unit='$school_id'") . " order by version desc"
);

$class_count = $data[0]['class'] ?? null;
$field = $data[0]['field'] ?? null;
$videos = $data[0]['videos'] ?? null;
$video_a = $data[0]['video_a'] ?? null;
$news = $data[0]['news'] ?? null;
$report = $data[0]['result'] ?? null;
$content = $data[0]['result_text'] ?? null;
$school_type = $data[0]['SchoolType'] ?? $school_type;

$method = $_POST['method'] ?? null;

if (!$readonly) {
  $class_count = $_POST['class_count'] ?? $class_count;
  $field = $_POST['field'] ?? $field;
  $video_a = $_POST['video_a'] ?? $video_a;

  if ($method === 'upload' || $state === '-1') {
    date_default_timezone_set("Asia/Taipei");
    $update_time = date('Y-m-d H:i:s');
    $version = ($data[0]['version'] ?? 0) + 1;
    $directory = "./upload/demo_result/$year-$month/$school_id";

    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }

    $news_file = $_FILES['news'] ?? null;
    $news = upload_file($news_file, "$directory/result_news_$version");
    $report_file = $_FILES['report'] ?? null;
    $report = upload_file($report_file, "$directory/result_report_$version");
    $content_file = $_FILES['content'] ?? null;
    $content = upload_file($content_file, "$directory/result_content_$version");
    $videos_file = $_POST['videos'] ?? $_FILES['videos'] ?? null;
    $videos = upload_file($videos_file, "$directory/result_videos_$version");

    insert(
      $connect,
      'demo_result',
      "'$project', '$year', '$month', '$school_type', '$state', '$class_count', '$field', '$videos', '$video_a', '$news', '$report', '$content', '$school_id', '$update_time', '$version'",
      'project, year, month, SchoolType, state, class, field, videos, video_a, news, result, result_text, unit, updatetime, version'
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
        <h1 class="text-2xl">當月實施成果</h1>
        <h3 class="text-xl text-error">* 請填寫''當月份''數值，非累計值 *</h3>
      </div>

      <form class="form-control" name="form" method="post" enctype="multipart/form-data" action="./y04.php#top">
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
        <?php if ($school_type == '1') { ?>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
            <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
              <label class="label gap-2">
                <span class="label-text flex-1">標竿課堂實施數(包含PBL實施節數)</span>
                <span class="label-text-alt text-error">必填欄位</span>
              </label>
              <input class="input input-sm input-bordered w-full" type="number" min="0" name="class_count" required value="<?= $class_count ?>" <?php if ($readonly) echo 'disabled' ?> />
            </div>
            <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
              <label class="label gap-2">
                <span class="label-text flex-1">導入教學學科領域(請填科目)：</span>
                <!-- <span class="label-text-alt text-error">必填欄位</span> -->
              </label>
              <input class="input input-sm input-bordered w-full" type="text" name="field" required value="<?= $field ?>" <?php if ($readonly) echo 'disabled' ?> />
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
            <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
              <label class="label gap-2">
                <span class="label-text flex-1">製作示範/特色教學影片：</span>
                <!-- <span class="label-text-alt text-error">必填欄位</span> -->
              </label>
              <?php if (!$readonly) { ?>
                <input id="videos" class="file-input file-input-bordered file-input-sm w-full" type="file" name="videos" onChange="set_text('videos')">
                <p id="videos_text" class="text-base opacity-50"></p>
                <input class="input input-sm input-bordered w-full" type="text" name="video_a" placeholder="輸入影片連結" value="<?= $video_a ?>" <?php if ($readonly) echo 'disabled' ?> />
              <?php } ?>
              <?php if ($videos) {
                $file_path = explode('/', $videos) ?>
                <a class="btn btn-sm btn-outline" href="<?= $videos ?>" download><?= end($file_path) ?></a>
              <?php } ?>
            </div>
            <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
              <label class="label gap-2">
                <span class="label-text flex-1">媒體報導：</span>
                <span class="label-text-alt text-error">必填欄位</span>
              </label>
              <?php if (!$readonly) { ?>
                <input id="news" class="file-input file-input-bordered file-input-sm w-full" type="file" name="news" required accept=".pdf" onChange="set_text('news')">
                <p id="news_text" class="text-base opacity-50"></p>
              <?php } ?>
              <?php if ($news) {
                $file_path = explode('/', $news) ?>
                <a class="btn btn-sm btn-outline" href="<?= $news ?>" download><?= end($file_path) ?></a>
              <?php } ?>
            </div>
          </div>
        <?php } else { ?>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
            <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
              <label class="label gap-2">
                <span class="label-text flex-1">課堂實施數：</span>
                <span class="label-text-alt text-error">必填欄位</span>
              </label>
              <input class="input input-sm input-bordered w-full" type="number" min="0" name="class_count" required value="<?= $class_count ?>" <?php if ($readonly) echo 'disabled' ?> />
            </div>
          </div>
        <?php } ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">整體計畫推動成果：</span>
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
          <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
            <label class="label gap-2">
              <span class="label-text flex-1">針對個別課堂實施成果(文字200以上加照片)</span>
              <span class="label-text-alt text-error">必填欄位</span>
            </label>
            <?php if (!$readonly) { ?>
              <input id="content" class="file-input file-input-bordered file-input-sm w-full" type="file" name="content" required accept=".pdf" onChange="set_text('content')">
              <p id="content_text" class="text-base opacity-50"></p>
            <?php } ?>
            <?php if ($content) {
              $file_path = explode('/', $content) ?>
              <a class="btn btn-sm btn-outline" href="<?= $content ?>" download><?= end($file_path) ?></a>
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