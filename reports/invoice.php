<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['doctor']) || !is_numeric($_GET['doctor'])
  || empty($_GET['month']) || !preg_match('/^[0-9]{4}-[0-9]{1,2}$/', $_GET['month']));

$doctor = fetch_one('SELECT * FROM contacts WHERE id=:id LIMIT 1', array(':id' => $_GET['doctor']));

not_found_if(empty($doctor));

$month = $_GET['month'];
$closeDateFrom = strtotime("{$month}-01");
$closeDateTo = strtotime("+1 month", $closeDateFrom) - 1;

$q = <<<SQL
SELECT
  t.*,
  p.name AS patientName,
  w.name AS worktypeName
FROM tasks t
LEFT JOIN worktypes w ON w.id=t.worktype
INNER JOIN contacts p ON p.id=t.patient
WHERE t.doctor=:doctor
AND t.closeDate <> 0
AND t.closeDate BETWEEN :closeDateFrom AND :closeDateTo
ORDER BY t.closeDate ASC, t.startDate ASC
SQL;

$tasks = fetch_all($q, array(
  ':doctor' => $doctor->id,
  ':closeDateFrom' => $closeDateFrom,
  ':closeDateTo' => $closeDateTo
));

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Faktura</title>
  <style>
    body {
      font: 1em/1.4 Arial, sans-serif;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      border: 1px solid #000;
      padding: .25em;
    }
  </style>
</head>
<body>
<h1>Faktura</h1>
<h2>Zadania dla <em><?= e($doctor->name) ?></em> z miesiąca <em><?= $month ?></em></h2>
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Nr</th>
      <th>Data rozpoczęcia</th>
      <th>Data oddania</th>
      <th>Typ</th>
      <th>Pacjent</th>
    </tr>
  </thead>
  <tbody>
    <? foreach ($tasks as $i => $task): ?>
    <tr>
      <td><?= $i + 1 ?>.</td>
      <td><?= e($task->nr) ?></td>
      <td><?= date('Y-m-d', $task->startDate) ?></td>
      <td><?= date('Y-m-d', $task->closeDate) ?></td>
      <td><?= e($task->worktypeName) ?></td>
      <td><?= e($task->patientName) ?></td>
    </tr>
    <? endforeach ?>
  </tbody>
</table>
</body>
</html>
