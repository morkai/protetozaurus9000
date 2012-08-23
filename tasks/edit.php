<?php

include __DIR__ . '/__common__.php';

bad_request_if(empty($_GET['id']));

$q = <<<SQL
SELECT
  t.*,
  d.name AS doctorName,
  p.name AS patientName
FROM tasks t
LEFT JOIN contacts d ON d.id=t.doctor
LEFT JOIN contacts p ON p.id=t.patient
WHERE t.id=:id
LIMIT 1
SQL;

$task = fetch_one($q, array(':id' => $_GET['id']));

not_found_if(empty($task));

if (!empty($_POST['task']))
{
  $data = tasks_prepare_data($_POST['task']);

  exec_update('tasks', $data, "id={$task->id}");

  set_flash('Zadanie zostało zmodyfikowane pomyślnie!');
  go_to(get_referer("/tasks/view.php?id={$task->id}"));
}

escape($task);

$task->startDate = date('Y-m-d', $task->startDate);
$task->closeDate = empty($task->closeDate) ? '' : date('Y-m-d', $task->closeDate);
$task->quantity = round($task->quantity, 2);

$worktypes = fetch_array('SELECT id AS `key`, name AS `value` FROM worktypes ORDER BY name ASC');
$colors = fetch_array('SELECT id AS `key`, name AS `value` FROM colors ORDER BY name ASC');

?>

<? decorate('Edycja zadania') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("/tasks") ?>">Zadania</a> \
    <a href="<?= url_for("/tasks/view.php?id={$task->id}") ?>"><?= $task->nr ?></a> \
    Edycja
  </h1>
</div>

<form action="<?= url_for("/tasks/edit.php?id={$task->id}") ?>" method=post autocomplete=off>
  <? include __DIR__ . '/__form__.php' ?>
</form>
