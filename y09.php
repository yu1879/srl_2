<?php
require_once('./database.php');
session_start();
header('Cache-Control: Private');

if (!$_SESSION['account']) {
    header("Location: ./login.php");
    exit();
}

if (isset($_SESSION['message'])) {
    echo "<script>alert('{$_SESSION['message']}')</script>";
    unset($_SESSION['message']);
}

function search($data, $search)
{
    $result = array();
    foreach ($data as $d) {
        if (str_contains($d['User'], $search)) {
            $result[] = $d;
        }
    }
    return $result;
}

$unit = $_SESSION['unit'];

$project = $_POST['project'] ?? '112-113年5G智慧學習推動計畫';
$year = $_POST['year'] ?? '112';
$month = $_POST['month'] ?? intval(date('n'));
$type = $_POST['type'] ?? '全部';
$search = $_POST['search'] ?? null;
$status = $_POST['status'] ?? '0';
$action = $_POST['action'] ?? null;

$connect = connect_sql();
if ($action === '1' && $status === '0') {
    unset($_POST['project']);
    unset($_POST['year']);
    unset($_POST['month']);
    unset($_POST['type']);
    unset($_POST['search']);
    unset($_POST['status']);
    unset($_POST['action']);
    $tables = array(
        '教師培訓人數' => 'demo_teachers',
        '實施成果' => 'demo_result',
        '公開授課紀錄' => 'demo_school',
        '教學成效評估' => 'demo_evaluate',
        '入校輔導紀錄' => 'demo_counseling'
    );
    foreach ($_POST as $key => $value) {
        $parameters = explode('_', $key);
        $condition = "where project='$project' and year='$year' and month='$month' and unit='$parameters[0]' and version='$parameters[2]'";
        $s = select($connect, $tables[$parameters[1]], 'state', $condition)[0]['state'];
        $s = $s === '-1' ? '-2' : '1';
        update($connect, $tables[$parameters[1]], "state='$s'", $condition);
    }
} else if ($action === '0' && $status === '1') {
    unset($_POST['project']);
    unset($_POST['year']);
    unset($_POST['month']);
    unset($_POST['type']);
    unset($_POST['search']);
    unset($_POST['status']);
    unset($_POST['action']);
    $tables = array(
        '教師培訓人數' => 'demo_teachers',
        '實施成果' => 'demo_result',
        '公開授課紀錄' => 'demo_school',
        '教學成效評估' => 'demo_evaluate',
        '入校輔導紀錄' => 'demo_counseling'
    );
    foreach ($_POST as $key => $value) {
        $parameters = explode('_', $key);
        $condition = "where project='$project' and year='$year' and month='$month' and unit='$parameters[0]' and version='$parameters[2]'";
        $s = select($connect, $tables[$parameters[1]], 'state', $condition)[0]['state'];
        $s = $s === '-2' ? '-1' : '0';
        update($connect, $tables[$parameters[1]], "state='$s'", $condition);
    }
}

$condition1 = "join (select unit, max(version) as ver from ";
$condition2 = " group by unit) b on a.unit=b.unit and a.version=b.ver join users on a.unit=users.sch_id where project='$project' and year='$year' and users.Unit='$unit' and month='$month'" . ($type !== '全部' ? " and SchoolType='{$type}'" : '') . " and (state='{$status}' or state='" . - ($status + 1) . "')";
$columns = 'SchoolType, state, version, a.unit unit, users.Unit Unit, User, sch_id';
$demo_teachers = select($connect, 'demo_teachers a', $columns, $condition1 . 'demo_teachers' . $condition2);
$demo_result = select($connect, 'demo_result a', $columns, $condition1 . 'demo_result' . $condition2);
$demo_school = select($connect, 'demo_school a', $columns, $condition1 . 'demo_school' . $condition2);
$demo_evaluate = select($connect, 'demo_evaluate a', $columns, $condition1 . 'demo_evaluate' . $condition2);
$demo_counseling = select($connect, 'demo_counseling a', $columns, $condition1 . 'demo_counseling' . $condition2);
if (!is_null($search) && $search !== '') {
    $demo_teachers = search($demo_teachers, $search);
    $demo_result = search($demo_result, $search);
    $demo_school = search($demo_school, $search);
    $demo_evaluate = search($demo_evaluate, $search);
    $demo_counseling = search($demo_counseling, $search);
}

$demo_count = count($demo_teachers) + count($demo_result) + count($demo_school) + count($demo_evaluate) + count($demo_counseling);
$demos = array(
    '教師培訓人數' => $demo_teachers,
    '實施成果' => $demo_result,
    '公開授課紀錄' => $demo_school,
    '教學成效評估' => $demo_evaluate,
    '入校輔導紀錄' => $demo_counseling
);

$check_pages = array(
    '教師培訓人數' => './y05.php',
    '實施成果' => './y04.php',
    '公開授課紀錄' => './y03.php',
    '教學成效評估' => './y13.php',
    '入校輔導紀錄' => './y02.php'
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
            <h1 class="text-2xl">填報審核</h1>

            <form class="form-control" name="form" method="post" action="./y09.php#top">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-4">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4 sm:col-span-2">
                        <p class="text-base">計畫名稱：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="project" onChange="auto_submit()">
                            <option value='112-113年5G智慧學習推動計畫' <?php if ($project === '112-113年5G智慧學習推動計畫') echo 'selected' ?>>112-113年5G智慧學習推動計畫</option>
                            <!-- <option value='112-113年數位學習推動計畫' <?php if ($project === '112-113年數位學習推動計畫') echo 'selected' ?>>112-113年數位學習推動計畫</option> -->
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <p class="text-bae">年度：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="year" onChange="auto_submit()">
                            <option value='112' <?php if ($year === '112') echo 'selected' ?>>112</option>
                            <option value='113' <?php if ($year === '113') echo 'selected' ?>>113</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <p class="text-base">填報月份：</p>
                        <select class="select select-bordered select-sm mt-1" name="month" onChange="auto_submit()">
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
                        <p class="text-base">類型：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="type" onChange="auto_submit()">
                            <option value='全部' <?php if ($type === '全部') echo 'selected' ?>>全部</option>
                            <option value='0' <?php if ($type === '0') echo 'selected' ?>>5G學校(示範)</option>
                            <option value='1' <?php if ($type === '1') echo 'selected' ?>>5G學校(標竿)</option>
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
                        <p class="text-base">學校搜尋：</p>
                        <div class="flex gap-2 mt-1">
                            <input class="input input-sm input-bordered w-full" type="search" name="search" value="<?= $search ?>">
                            <button class="btn btn-sm w-24">搜尋</button>
                        </div>
                    </div>
                </div>
            </form>
            <?php if ($demo_count === 0) { ?>
                <div class="p-8">
                    <p class="text-2xl text-error">查無資料</p>
                </div>
            <?php } else { ?>
                <div class="overflow-x-auto w-full mt-8">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="bg-base-100"><input class="checkbox" type="checkbox" id="check_all" onChange="check_all()"></th>
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
                            <?php foreach ($demos as $name => $demo) { ?>
                                <?php foreach ($demo as $d) { ?>
                                    <tr>
                                        <th><input class="checkbox" type="checkbox" name="<?= $d['unit'] . '_' . $name . '_' . $d['version'] ?>" form="action_form"></th>
                                        <td><?= $d['Unit'] ?></td>
                                        <td><?= $d['User'] ?></td>
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
                                                    <input type="hidden" name="school_id" value="<?= $d['sch_id'] ?>">
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
                        <input type="hidden" name="search" value="<?= $search ?>">
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
            const form = document.forms['form'];
            form.submit();
        }

        function check_all() {
            const checked = document.getElementById('check_all').checked;
            const inputs = document.querySelectorAll('tbody input[type=checkbox]');
            inputs.forEach(input => {
                input.checked = checked;
            });
        }
    </script>
</body>


</html>