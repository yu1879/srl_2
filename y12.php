<?php
require_once('./database.php');
session_start();
header('Cache-Control: Private');

if (!$_SESSION['account']) {
    header("Location: ./login.php");
    exit();
}
$project = $_POST['project'] ?? '112-113年5G智慧學習推動計畫';
$year = $_POST['year'] ?? '112';
$month = $_POST['month'] ?? intval(date('n'));
$unit = $_POST['unit'] ?? '全部';
$report = $_POST['report'] ?? '全部';
$status = $_POST['status'] ?? '0';
$action = $_POST['action'] ?? null;

$connect = connect_sql();
if ($action === '1' && $status === '0')
{
    unset($_POST['project']);
    unset($_POST['year']);
    unset($_POST['month']);
    unset($_POST['unit']);
    unset($_POST['report']);
    unset($_POST['status']);
    unset($_POST['action']);

    $tables = array(
        '教師培訓人數' => 'demo_teachers',
        '實施結果' => 'demo_result',
        '公開授課' => 'demo_public',
        '入校輔導' => 'demo_counseling',
        '縣市教育局處每月填報' => 'pu021',
        '效益執行說明(季報)' => 'pu023'
    );
    
    foreach ($_POST as $key => $value) {
        $parameters = explode('_', $key);
        $condition = "where project='$project' and year='$year' and month='$month' and unit='$parameters[0]' and version='$parameters[2]'";
        $s = select($connect, $tables[$parameters[1]], 'state', $condition)[0]['state'];
        $s = $s === '-1' ? '-2' : '1';
        update($connect, $tables[$parameters[1]], "state='$s'", $condition);
    }
}
else if($action === '0' && $status === '1')
{
    unset($_POST['project']);
    unset($_POST['year']);
    unset($_POST['month']);
    unset($_POST['unit']);
    unset($_POST['report']);
    unset($_POST['status']);
    unset($_POST['action']);

    $tables = array(
        '教師培訓人數' => 'demo_teachers',
        '實施結果' => 'demo_result',
        '公開授課' => 'demo_public',
        '入校輔導' => 'demo_counseling',
        '縣市教育局處每月填報' => 'pu021',
        '效益執行說明(季報)' => 'pu023'
    );

    foreach ($_POST as $key => $value) {
        $parameters = explode('_', $key);
        $condition = "where project='$project' and year='$year' and month='$month' and unit='$parameters[0]' and version='$parameters[2]'";
        $s = select($connect, $tables[$parameters[1]], 'state', $condition)[0]['state'];
        $s = $s === '-2' ? '-1' : '0';
        update($connect, $tables[$parameters[1]], "state='$s'", $condition);
    }
}

$condition = "join school_detail on a.Unit=sch_id where project='$project' and year='$year' and month='$month' and (state='$status' or state='".(-$status+1)."')";
$demo_teachers = array();
if($report === '全部' || $report === '教師培訓人數')
{
  $demo_teachers = select($connect, 'demo_teachers a', '*', $condition);
}
$demo_result = array();
if($report === '全部' || $report === '實施結果')
{
  $demo_result = select($connect, 'demo_result a', '*', $condition);
}
$demo_public = array();
// if($report === '全部' || $report === '公開授課')
// {
//   $demo_public = select($connect, 'demo_public a', '*', $condition);
// }
$demo_counseling = array();
if($report === '全部' || $report === '入校輔導')
{
  $demo_counseling = select($connect, 'demo_counseling a', '*', $condition);
}
$pu021 = array();
if($report === '全部' || $report === '縣市教育局處每月填報')
{
  $pu021 = select($connect, 'PU021 a', '*', "join school_detail on a.Unit=sch_id where year='$year' and month='$month' and (state='$status' or state='".(-$status+1)."')");
}
$pu023 = array();
if($report === '全部' || $report === '效益執行說明(季報)')
{
  $pu023 = select($connect, 'PU023 a', '*', "join school_detail on a.Unit=sch_id where year='$year' and season='$month' and (state='$status' or state='".(-$status+1)."')");
}
$data_count = count($demo_teachers) + count($demo_result) + count($demo_public) + count($demo_counseling) + count($pu021) + count($pu021) + count($pu023);
$datas = array(
  '教師培訓人數' => $demo_teachers,
  '實施結果' => $demo_result,
  '公開授課' => $demo_public,
  '入校輔導' => $demo_counseling,
  '縣市教育局處每月填報' => $pu021,
  '效益執行說明(季報)' => $pu023
);
$check_pages = array(
  '教師培訓人數' => './y05.php',
  '實施結果' => './y04.php',
  '公開授課' => './y03.php',
  '入校輔導' => './y02.php',
  '縣市教育局處每月填報' => './y06.php',
  '效益執行說明(季報)' => './y07.php'
);
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
            <h1 class="text-2xl">填報資料</h1>

            <form class="form-control" name="form" method="post" action="./y12.php#top">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-4">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4 sm:col-span-2">
                        <p class="text-base">計畫名稱：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="project" onChange="auto_submit()">
                            <option value='112-113年5G智慧學習推動計畫' <?php if ($project === '112-113年5G智慧學習推動計畫') echo 'selected' ?>>112-113年5G智慧學習推動計畫</option>
                            <!-- <option value='112-113年數位學習推動計畫' <?php if ($project === '112-113年數位學習推動計畫') echo 'selected' ?>>112-113年數位學習推動計畫</option> -->
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <p class="text-base">年度：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="year" onChange="auto_submit()">
                            <option value='112' <?php if ($year === '112') echo 'selected' ?>>112</option>
                            <option value='113' <?php if ($year === '113') echo 'selected' ?>>113</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <p class="text-base">填報月份：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="month" onChange="auto_submit()">
                            <?php
                            for ($m = 1; $m <= 12; $m++) {
                                if ($m == $month) {
                                    echo "<option value='$m' selected='selected'>{$m}月</option>";
                                } else {
                                    echo "<option value='$m'>{$m}月</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <p class="text-base">單位：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="unit" onChange="auto_submit()">
                            <option value='全部' <?php if ($unit === '全部') echo 'selected' ?>>全部</option>
                            <option value='縣市' <?php if ($unit === '縣市') echo 'selected' ?>>縣市</option>
                            <option value='學校' <?php if ($unit === '學校') echo 'selected' ?>>學校</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <p class="text-base">審核狀態：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="status" onChange="auto_submit()">
                            <option value='0' <?php if ($status === '0') echo 'selected' ?>>未審核</option>
                            <option value='1' <?php if ($status === '1') echo 'selected' ?>>已審核</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4 sm:col-span-2">
                        <p class="text-base">填報資料：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="report" onChange="auto_submit()">
                            <option value='全部' <?php if ($report === '全部') echo 'selected' ?>>全部</option>
                            <option value='教師培訓人數' <?php if ($report === '教師培訓人數') echo 'selected' ?>>教師培訓人數</option>
                            <option value='實施結果' <?php if ($report === '實施結果') echo 'selected' ?>>實施結果</option>
                            <option value='公開授課' <?php if ($report === '公開授課') echo 'selected' ?>>公開授課</option>
                            <option value='入校輔導' <?php if ($report === '入校輔導') echo 'selected' ?>>入校輔導</option>
                            <option value='縣市教育局處每月填報' <?php if ($report === '縣市教育局處每月填報') echo 'selected' ?>>縣市教育局處每月填報</option>
                            <option value='效益執行說明(季報)' <?php if ($report === '效益執行說明(季報)') echo 'selected' ?>>效益執行說明(季報)</option>
                        </select>
                    </div>
                </div>
            </form>
            <?php if ($data_count === 0) { ?>
                <div class="p-8">
                    <p class="text-2xl text-error">查無資料</p>
                </div>
            <?php } else { ?>
                <div class="overflow-x-auto w-full mt-8">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="bg-base-100"><input class="checkbok" type="checkbox" id="checkall"></th>
                                <th class="bg-base-100">縣市</th>
                                <th class="bg-base-100">單位</th>
                                <th class="bg-base-100">類型</th>
                                <th class="bg-base-100">年度</th>
                                <th class="bg-base-100">月份</th>
                                <th class="bg-base-100">填報資料</th>
                                <th class="bg-base-100"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($datas as $name => $data) { ?>
                                <?php foreach ($data as $d) {
                                    print_r($d) ?>
                                    <tr>
                                        <th><input class="checkbox" type="checkbox" name="<?= $d['unit'] . '_' . $name . '_' . $d['version'] ?>" form="action_form"></th>
                                        <td><?= $d['Unit'] ?></td>
                                        <td><?= $d['school'] ?></td>
                                        <td><?= $d['SchoolType'] === '1' ? '5G學校(標竿)' : '5G學校(示範)' ?></td>
                                        <td><?= $year ?></td>
                                        <td><?= $month ?>月</td>
                                        <td><?= $name ?></td>
                                        <td>
                                            <?php if ($d['state'] < 0) {
                                                echo '未辦理';
                                            } else { ?>
                                                <form method="post" action="<?= $check_pages[$name] ?>">
                                                    <input type="hidden" name="project" value="<?= $project ?>">
                                                    <input type="hidden" name="year" value="<?= $year ?>">
                                                    <input type="hidden" name="month" value="<?= $month ?>">
                                                    <input type="hidden" name="school_id" value="<?= $d['Unit'] ?>">
                                                    <button class="btn btn-sm">查看</button>
                                                </form>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="bg-base-100">#</th>
                                <th class="bg-base-100">縣市</th>
                                <th class="bg-base-100">單位</th>
                                <th class="bg-base-100">類型</th>
                                <th class="bg-base-100">年度</th>
                                <th class="bg-base-100">月份</th>
                                <th class="bg-base-100">填報資料</th>
                                <th class="bg-base-100"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="p-4">
                    <form id="action_form" method="post" action="./y09.php#top">
                        <input type="hidden" name="project" value="<?= $project ?>">
                        <input type="hidden" name="year" value="<?= $year ?>">
                        <input type="hidden" name="month" value="<?= $month ?>">
                        <input type="hidden" name="type" value="<?= $type ?>">
                        <input type="hidden" name="status" value="<?= $status ?>">
                        <div class="flex gap-2">
                            <button class="btn btn-sm w-24" name="action" value="1">核准</button>
                            <button class="btn btn-sm w-24" name="action" value="0">退回</button>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        function auto_submit() {
            form = document.forms['form'];
            form.submit();
        }
    </script>
</body>

</html>