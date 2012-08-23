<?php

include __DIR__ . '/__common__.php';

bad_request_if(empty($_GET['id']));

$q = <<<SQL
SELECT
  t.*,
  d.name AS doctorName,
  p.name AS patientName,
  c.name AS colorName,
  w.name AS worktypeName
FROM tasks t
LEFT JOIN contacts d ON d.id=t.doctor
LEFT JOIN contacts p ON p.id=t.patient
INNER JOIN colors c ON c.id=t.color
INNER JOIN worktypes w ON w.id=t.worktype
WHERE t.id=:id
LIMIT 1
SQL;

$task = fetch_one($q, array(':id' => $_GET['id']));

not_found_if(empty($task));

$task->startDate = date('Y-m-d', $task->startDate);
$task->closeDate = empty($task->closeDate) ? '-' : date('Y-m-d', $task->closeDate);
$task->notes = empty($task->notes) ? '-' : nl2br(e($task->notes));
$task->quantity = round($task->quantity, 2);

?>

<? decorate('Zadanie') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for("/tasks/edit.php?id={$task->id}") ?>"><i class="icon-pencil"></i> Edytuj</a>
    <li><a class="btn btn-danger" href="<?= url_for("/tasks/delete.php?id={$task->id}") ?>"><i class="icon-remove icon-white"></i> Usuń</a>
  </ul>
  <h1>
    <a href="<?= url_for("/tasks") ?>">Zadania</a> \
    <?= e($task->nr) ?>
  </h1>
</div>

<dl class="well properties">
  <dt>Nr:
  <dd><?= e($task->nr) ?>
  <dt>Data rozpoczęcia:
  <dd><?= $task->startDate ?>
  <dt>Data oddania:
  <dd><?= $task->closeDate ?>
  <dt>Lekarz:
  <dd>
    <? if (empty($task->doctor)): ?>
    -
    <? else: ?>
    <a href="<?= url_for("contacts/view.php?id={$task->doctor}") ?>"><?= e($task->doctorName) ?></a>
    <? endif ?>
  <dt>Pacjent:
  <dd>
    <? if (empty($task->patient)): ?>
    -
    <? else: ?>
    <a href="<?= url_for("contacts/view.php?id={$task->patient}") ?>"><?= e($task->patientName) ?></a>
    <? endif ?>
  <dt>Typ pracy:
  <dd><?= e($task->worktypeName) ?>
  <dt>Ilość:
  <dd><?= $task->quantity ?> <?= e($task->unit) ?>
  <dt>Cena:
  <dd><?= $task->price ?> zł
  <dt>Kolor:
  <dd><?= e($task->colorName) ?>
  <dt>Zęby:
  <dd><? tasks_render_teeth($task->teeth, true) ?>
  <dt>Uwagi:
  <dd><?= $task->notes ?>
</dl>
