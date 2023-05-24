<?php
require_once('./database.php');
session_start();
header('Cache-Control: Private');

$unit = $_SESSION['unit'] ?? null;

$connect = connect_sql();
$schools = select($connect, 'school_detail', 'School, SchoolType, SchoolLevel, sch_id', "where unit = '$unit' order by sch_id");
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
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

    <div class="space-y-4 p-4 sm:p-8">
      <div class="flex justify-between gap-4">
        <div class="btn-group">
          <button class="btn btn-sm" onclick="changeType('0')">示範學校</button>
          <button class="btn btn-sm" onclick="changeType('1')">標竿學校</button>
        </div>
        <button type="button" class="btn btn-sm" onclick="exportExcel()">下載 Excel 報表</button>
      </div>
      <div id="charts" class="space-y-4 sm:space-y-8">
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="一般教案"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="短期學校成效"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="學生自主學習量表"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="學生5C關鍵能力意向量表"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="數位學習教師增能培訓"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="資訊組長增能培訓"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="5G應用之教學培訓"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="辦理公開授課"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="訪視輔導"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="跨校縣市公開授課"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="參加成果推廣"></canvas>
        </div>
      </div>
    </div>
  </div>

  <script>
    let schoolType = '0';
    let data = [];

    const schools = <?php echo json_encode($schools) ?>;
    changeType(schoolType)

    function changeType(type) {
      schoolType = type;

      const items = schools.filter(item => item.SchoolType === type);

      let kpiGoal = [];
      let labels = [];

      if (type === '0') {
        // 各圖表 kpi 目標值
        kpiGoal = [2, 2, 2, 2, [10, 10, 2], 0, 2, [1, 1, 1],
          [1, 1], 2, 2
        ];

        charts = [
          '一般教案',
          '短期學校成效',
          '學生自主學習量表',
          '學生5C關鍵能力意向量表',
          '數位學習教師增能培訓',
          '資訊組長增能培訓',
          '5G應用之教學培訓',
          '辦理公開授課',
          '訪視輔導',
          '跨校縣市公開授課',
          '參加成果推廣'
        ];

        // 各校名稱
        labels = items.map(item => item.School);

        // 產生各校隨機假資料
        data = items.map(item => [
          Math.floor(Math.random() * 6),
          Math.floor(Math.random() * 6),
          Math.floor(Math.random() * 6),
          Math.floor(Math.random() * 6),
          [
            Math.floor(Math.random() * 30),
            Math.floor(Math.random() * 30),
            Math.floor(Math.random() * 6),
          ],
          0,
          Math.floor(Math.random() * 6),
          [
            Math.floor(Math.random() * 2),
            Math.floor(Math.random() * 4),
            Math.floor(Math.random() * 4),
          ],
          [
            Math.floor(Math.random() * 4),
            Math.floor(Math.random() * 4),
          ],
          Math.floor(Math.random() * 6),
          Math.floor(Math.random() * 6),
        ]);

        document.getElementById('charts').innerHTML = `
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="一般教案"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="短期學校成效"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="學生自主學習量表"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="學生5C關鍵能力意向量表"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="數位學習教師增能培訓"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="資訊組長增能培訓"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="5G應用之教學培訓"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="辦理公開授課"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="訪視輔導"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="跨校縣市公開授課"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="參加成果推廣"></canvas>
        </div>`;
      } else {
        // 各圖表 kpi 目標值
        kpiGoal = [6, 0, 3, 2, 3, [15, 15, 4], 0, 4, [3, 3, 3],
          [0, 0], 4, 4, 1, 2
        ];

        charts = [
          '一般教案',
          'PBL教案',
          '短期學校成效',
          '學生自主學習量表',
          '學生5C關鍵能力意向量表',
          '數位學習教師增能培訓',
          '資訊組長增能培訓',
          '5G應用之教學培訓',
          '辦理公開授課',
          '訪視輔導',
          '跨校縣市公開授課',
          '參加成果推廣',
          '製作示範教學影片',
          '發布新聞媒體報導'
        ];

        // 各校名稱
        labels = items.map(item => item.School);

        // 產生各校隨機假資料
        data = items.map(item => [
          Math.floor(Math.random() * 18),
          0,
          Math.floor(Math.random() * 9),
          Math.floor(Math.random() * 6),
          Math.floor(Math.random() * 9),
          [
            Math.floor(Math.random() * 40),
            Math.floor(Math.random() * 40),
            Math.floor(Math.random() * 12),
          ],
          0,
          Math.floor(Math.random() * 12),
          [
            Math.floor(Math.random() * 5),
            Math.floor(Math.random() * 5),
            Math.floor(Math.random() * 5),
          ],
          [0, 0],
          Math.floor(Math.random() * 12),
          Math.floor(Math.random() * 12),
          Math.floor(Math.random() * 3),
          Math.floor(Math.random() * 4),
        ]);

        document.getElementById('charts').innerHTML = `
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="一般教案"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="PBL教案"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="短期學校成效"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="學生自主學習量表"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="學生5C關鍵能力意向量表"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="數位學習教師增能培訓"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="資訊組長增能培訓"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="5G應用之教學培訓"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="辦理公開授課"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="訪視輔導"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="跨校縣市公開授課"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="參加成果推廣"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="製作示範教學影片"></canvas>
        </div>
        <div class="p-2 bg-base-200 rounded-lg">
          <canvas id="發布新聞媒體報導"></canvas>
        </div>`;
      }

      charts.forEach((id, index) => {
        const ctx = document.getElementById(id);

        let datasets = [{
          label: id,
          data: data.map(item => item[index]),
          borderWidth: 1
        }];

        if (id === '數位學習教師增能培訓') {
          datasets = [{
            label: 'A1',
            data: data.map(item => item[index][0]),
            borderWidth: 1
          }, {
            label: 'A2',
            data: data.map(item => item[index][1]),
            borderWidth: 1
          }, {
            label: 'B1',
            data: data.map(item => item[index][2]),
            borderWidth: 1
          }]
        } else if (id === '辦理公開授課') {
          datasets = [{
            label: '第一次',
            data: data.map(item => item[index][0]),
            borderWidth: 1
          }, {
            label: '第二次',
            data: data.map(item => item[index][1]),
            borderWidth: 1
          }, {
            label: '第三次',
            data: data.map(item => item[index][2]),
            borderWidth: 1
          }, {
            label: '總計',
            data: data.map(item => item[index][0] + item[index][1] + item[index][2]),
            borderWidth: 1
          }]
        } else if (id === '訪視輔導') {
          datasets = [{
            label: '第一次',
            data: data.map(item => item[index][0]),
            borderWidth: 1
          }, {
            label: '第二次',
            data: data.map(item => item[index][1]),
            borderWidth: 1
          }, {
            label: '總計',
            data: data.map(item => item[index][0] + item[index][1]),
            borderWidth: 1
          }]
        }

        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: datasets
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'bottom'
              },
              title: {
                display: true,
                text: `${id} - 目標：${kpiGoal[index]}`,
                font: {
                  size: 18
                }
              }
            },
            scales: {
              y: {
                ticks: {
                  stepSize: 1
                },
                grid: {
                  lineWidth: 2,
                  color: (context) => {
                    if (context.tick.value === kpiGoal[index]) {
                      return '#f43f5e';
                    } else if (context.tick.value === kpiGoal[index] * 2) {
                      return '#be123c';
                    }

                    return 'transparent';
                  },
                },
              }
            }
          }
        });
      });
    }

    function exportExcel() {
      const items = schools.filter(item => item.SchoolType === schoolType).map(item => item.School);
      let value = [];

      if (schoolType === '0') {
        value = [
          [
            '',
            '一般教案',
            '短期學校成效',
            '學生自主學習量表',
            '學生5C關鍵能力意向量表',
            '數位學習教師增能培訓',
            '',
            '',
            '資訊組長增能培訓',
            '5G應用之教學培訓',
            '辦理公開授課',
            '',
            '',
            '',
            '訪視輔導',
            '',
            '',
            '跨校縣市公開授課',
            '參加成果推廣'
          ],
          [
            '學校',
            '',
            '',
            '',
            '',
            'A1',
            'A2',
            'B1',
            '',
            '',
            '第一次',
            '第二次',
            '第三次',
            '總計',
            '第一次',
            '第二次',
            '總計',
            '',
            ''
          ]
        ]
      } else {
        value = [
          [
            '',
            '一般教案',
            'PBL教案',
            '短期學校成效',
            '學生自主學習量表',
            '學生5C關鍵能力意向量表',
            '數位學習教師增能培訓',
            '',
            '',
            '資訊組長增能培訓',
            '5G應用之教學培訓',
            '辦理公開授課',
            '',
            '',
            '',
            '訪視輔導',
            '',
            '',
            '跨校縣市公開授課',
            '參加成果推廣',
            '製作示範教學影片',
            '發布新聞媒體報導'
          ],
          [
            '學校',
            '',
            '',
            '',
            '',
            '',
            'A1',
            'A2',
            'B1',
            '',
            '',
            '第一次',
            '第二次',
            '第三次',
            '總計',
            '第一次',
            '第二次',
            '總計',
            '',
            '',
            '',
            ''
          ]
        ]
      }

      value = value.concat(items.map((label, i) => ([
        label, ...data[i].map((item, j) => {
          if (schoolType === '0') {
            if (j === 7)
              return [item[0], item[1], item[2], item[0] + item[1] + item[2]];
            else if (j === 8)
              return [item[0], item[1], item[0] + item[1]];
          } else {
            if (j === 8)
              return [item[0], item[1], item[2], item[0] + item[1] + item[2]];
            else if (j === 9)
              return [item[0], item[1], item[0] + item[1]];
          }

          return item
        }).reduce((x, y) => x.concat(y), [])
      ])));

      const wb = XLSX.utils.book_new();
      const ws = XLSX.utils.aoa_to_sheet([]);

      XLSX.utils.sheet_add_aoa(ws, value, 'A1');
      XLSX.utils.book_append_sheet(wb, ws);
      XLSX.writeFile(wb, `kpi.xlsx`);
    }
  </script>
</body>

</html>