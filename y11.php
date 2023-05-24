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

$unit = $_SESSION['unit'] ?? null;

$project = $_POST['project'] ?? '112-113年5G智慧學習推動計畫';
$year = $_POST['year'] ?? '112';
$month = $_POST['month'] ?? date('n');
$status = $_POST['status'] ?? '0';
if ((int)$month + 1 > 12) {
    $deadline = date((int)$year + 1 . '-1-5');
} else {
    $deadline = date($year . '-' . (int)$month + 1 . '-5');
}

$connect = connect_sql();
$data = array();
if($project === '112-113年5G智慧學習推動計畫')
{
    $condition = "where year='$year' and month='$month' and unit='$unit' order by version desc"; // add projectid
    $PU021 = select($connect, 'PU021', '*', $condition);
    $condition = "where year='$year' and season='$month' and unit='$unit' order by version desc"; // add projectid
    $PU023 = select($connect, 'PU023', '*', $condition);

    if(!($status === '0' xor count($PU021) === 0))
    {
        $data['縣市教育處每月填報'] = $PU021;
    }
    if(in_array($month, array('3', '6', '9', '12')) && !($status === '0' xor count($PU023) === 0))
    {
        $data['效益執行說明（季報）'] = $PU023;
    }

    $pages = array(
        '縣市教育處每月填報' => 'y06.php',
        '效益執行說明（季報）' => 'y07.php'
    );
}
else if($project === '112-113年5G新科技學習示範學校計畫')
{
    $condition = "where year='$year' and month='$month' and unit='$unit' order by version desc";
    $goal_1 = select($connect, 'goal_1', '*', $condition);
    $teacher_1 = select($connect, 'teacher_1', '*', $condition);
    $around = select($connect, 'around', '*', $condition);
    $lesson = select($connect, 'lesson', '*', $condition);
    $public_1 = select($connect, 'public_1', '*', $condition);
    $conseling_1 = select($connect, 'conseling_1', '*', $condition);
    $result_1 = select($connect, 'result_1', '*', $condition);

    // if(!($status === '0' xor count($goal_1) === 0))
    // {
    //     $data['目標值'] = $goal_1;
    // }
    if(!($status === '0' xor count($teacher_1) === 0))
    {
        $data['教師培訓'] = $teacher_1;
    }
    if(!($status === '0' xor count($around) === 0))
    {
        $data['新科技應用'] = $around;
    }
    if(!($status === '0' xor count($lesson) === 0))
    {
        $data['共學課程實施'] = $lesson;
    }
    if(!($status === '0' xor count($public_1) === 0))
    {
        $data['公開觀課'] = $public_1;
    }
    if(!($status === '0' xor count($conseling_1) === 0))
    {
        $data['入校輔導'] = $conseling_1;
    }
    if(!($status === '0' xor count($result_1) === 0))
    {
        $data['成果展現與推廣活動'] = $result_1;
    }
    if(!($status === '0' xor count($result_1) === 0))
    {
        $data['成效評估'] = $result_1;
    }

    $pages = array(
        // '目標值' => 'y21.php',
        '教師培訓' => 'y22.php',
        '新科技應用' => 'y23.php',
        '共學課程實施' => 'y24.php',
        '公開觀課' => 'y25.php',
        '入校輔導' => 'y26.php',
        '成果展現與推廣活動' => 'y27.php',
        '成效評估' => 'y29.php'

    );
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
            <h1 class="text-2xl">填報資料</h1>

            <form class="form-control" name="form" method="post" action="./y11.php#top">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-4">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4 sm:col-span-2">
                        <p class="text-base">計畫名稱：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="project" onChange="auto_submit()">
                            <option value='112-113年5G智慧學習推動計畫' <?php if ($project === '112-113年5G智慧學習推動計畫') echo 'selected' ?>>112-113年5G智慧學習推動計畫</option>
                            <option value='112-113年5G新科技學習示範學校計畫' <?php if ($project === '112-113年5G新科技學習示範學校計畫') echo 'selected' ?>>112-113年5G新科技學習示範學校計畫</option>
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
                        <p class="text-base">填報狀態：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="status" onChange="auto_submit()">
                            <option value='0' <?php if ($status === '0') echo 'selected' ?>>未填報</option>
                            <option value='1' <?php if ($status === '1') echo 'selected' ?>>已填報</option>
                        </select>
                    </div>
                </div>
            </form>
            <?php if (count($data) == 0) { ?>
                <div class="p-8">
                    <p class="text-2xl text-error">查無資料</p>
                </div>
            <?php } else { ?>
                <div class="overflow-x-auto w-full mt-8">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="bg-base-100">#</th>
                                <th class="bg-base-100">名稱</th>
                                <th class="bg-base-100">年度</th>
                                <th class="bg-base-100">月份</th>
                                <th class="bg-base-100">截止時間</th>
                                <th class="bg-base-100"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($data as $name => $table)
                            {
                            ?>
                                <tr>
                                    <th>#</th>
                                    <td><?=$name?></td>
                                    <td><?= $year ?></td>
                                    <td><?= $month ?>月</td>
                                    <td><?= $deadline ?></td>
                                    <td>
                                        <form method="post" action="./<?=$pages[$name]?>">
                                            <input type="hidden" name="project" value="<?= $project ?>">
                                            <input type="hidden" name="year" value="<?= $year ?>">
                                            <input type="hidden" name="month" value="<?= $month ?>">
                                            <div class="flex gap-2">
                                                <button class="btn btn-sm w-24"><?= $status === '1' && $table[0]['state'] === '-1' ? '未辦理' : ($status === '1' ? '修改' : '填報') ?></button>
                                                <?php if ($status === '0') { ?>
                                                    <button class="btn btn-sm w-24" name="state" value="-1">未辦理</button>
                                                <?php } ?>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="bg-base-100">#</th>
                                <th class="bg-base-100">名稱</th>
                                <th class="bg-base-100">年度</th>
                                <th class="bg-base-100">月份</th>
                                <th class="bg-base-100">截止時間</th>
                                <th class="bg-base-100"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        function auto_submit() {
            const form = document.forms['form'];
            form.submit();
        }
    </script>
</body>

</html>