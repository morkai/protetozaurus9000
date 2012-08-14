<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

$q = <<<SQL
SELECT
  t.*,
  d.name AS doctorName,
  p.name AS patientName,
  q.name AS quantityName,
  c.name AS colorName,
  w.name AS worktypeName
FROM tasks t
LEFT JOIN contacts d ON d.id=t.doctor
LEFT JOIN contacts p ON p.id=t.patient
INNER JOIN quantities q ON q.id=t.quantity
INNER JOIN colors c ON c.id=t.color
INNER JOIN worktypes w ON w.id=t.worktype
WHERE t.id=:id
LIMIT 1
SQL;

$task = fetch_one($q, array(':id' => $_GET['id']));

not_found_if(empty($task));

$task->startDate = date('Y-m-d', $task->startDate);
$task->closeDate = empty($task->closeDate) ? '-' : date('Y-m-d', $task->closeDate);
$task->metal = $task->metal ? 'Tak' : 'Nie';
$task->zircon = $task->zircon ? 'Tak' : 'Nie';
$task->notes = empty($task->notes) ? '-' : nl2br($task->notes);

?>

<? decorate('Zadanie') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for("/tasks/edit.php?id={$task->id}") ?>"><i class="icon-pencil"></i> Edytuj</a>
    <li><a class="btn btn-danger" href="<?= url_for("/tasks/delete.php?id={$task->id}") ?>"><i class="icon-remove icon-white"></i> Usuń</a>
  </ul>
  <h1>
    <a href="<?= url_for("/tasks") ?>">Zadania</a> \
    <?= $task->nr ?>
  </h1>
</div>

<dl class="well properties">
  <dt>Nr:
  <dd><?= $task->nr ?>
  <dt>Data rozpoczęcia:
  <dd><?= $task->startDate ?>
  <dt>Data oddania:
  <dd><?= $task->closeDate ?>
  <dt>Lekarz:
  <dd>
    <? if (empty($task->doctor)): ?>
    -
    <? else: ?>
    <a href="<?= url_for("contacts/view.php?id={$task->doctor}") ?>"><?= $task->doctorName ?></a>
    <? endif ?>
  <dt>Pacjent:
  <dd>
    <? if (empty($task->patient)): ?>
    -
    <? else: ?>
    <a href="<?= url_for("contacts/view.php?id={$task->patient}") ?>"><?= $task->patientName ?></a>
    <? endif ?>
  <dt>Typ pracy:
  <dd><?= $task->worktypeName ?>
  <dt>Ilość:
  <dd><?= $task->quantityName ?>
  <dt>Kolor:
  <dd><?= $task->colorName ?>
  <dt>Uwagi:
  <dd><?= $task->notes ?>
</dl>
