<?php
$user_level = $_SESSION['user_level'];
$sidebar_parameters = array();

// 學校
if ($user_level === '1') {
  $sidebar_parameters = array('填報資料' => './y10.php');
}
// 縣市
else if ($user_level === '2') {
  $sidebar_parameters = array(
    '填報資料' => './y11.php',
    '填報審核' => './y09.php',
    'KPI圖表' => './kpi.php'
  );
} else if ($user_level === '3') {
} else if ($user_level === '4') {
} else if ($user_level === '5') {
}
?>

<div>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4">
    <div class="flex items-center justify-start gap-4">
      <img src="./images/logo.png" class="h-8" />
      <p class="text-2xl"><?= $_SESSION['user'] ?></p>
    </div>
    <div class="flex items-center justify-end gap-2">
      <a href="./password.php" class="btn btn-sm w-24">變更密碼</a>
      <a href="./logout.php" class="btn btn-sm w-24">登出</a>
    </div>
  </div>
  <img src="./images/banner.png" />
  <div class="flex gap-2 p-4 max-w-3xl mx-auto">
    <?php foreach ($sidebar_parameters as $_title => $_link) { ?>
      <a href="<?= $_link ?>" class="btn btn-sm btn-primary w-24"><?= $_title ?></a>
    <?php } ?>
  </div>
</div>