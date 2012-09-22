<?php

include __DIR__ . '/__common__.php';

bad_request_if(empty($_GET['id']));

$q = <<<SQL
SELECT i.*
FROM invoices i
WHERE i.id=:invoice
LIMIT 1
SQL;

$invoice = fetch_one($q, array(':invoice' => $_GET['id']));

not_found_if(empty($invoice));

$q = <<<SQL
SELECT t.*,
p.name AS patientName,
w.name AS worktypeName
FROM invoice_tasks it
INNER JOIN tasks t ON t.id=it.task
INNER JOIN contacts p ON p.id=t.patient
INNER JOIN worktypes w ON w.id=t.worktype
WHERE it.invoice=:invoice
ORDER BY t.id ASC
SQL;

$invoice->tasks = fetch_all($q, array(':invoice' => $invoice->id));

escape($invoice);

?>

<? decorate('Faktura') ?>

<div class="page-header">
  <ul class="page-actions">
    <? if ($invoice->closed): ?>
    <li><a class="btn" href="<?= url_for("invoices/print.php?id={$invoice->id}") ?>"><i class="icon-print"></i> Drukuj</a>
    <? else: ?>
    <li><a class="btn" href="<?= url_for("invoices/close.php?id={$invoice->id}") ?>"><i class="icon-lock"></i> Zamknij i drukuj</a>
      <li><a class="btn" href="<?= url_for("invoices/edit.php?id={$invoice->id}") ?>"><i class="icon-pencil"></i> Edytuj</a>
      <li><a class="btn" href="<?= url_for("invoices/tasks.php?invoice={$invoice->id}") ?>"><i class="icon-random"></i> Przypisz zadania</a>
    <li><a class="btn btn-danger" href="<?= url_for("invoices/delete.php?id={$invoice->id}") ?>"><i class="icon-remove icon-white"></i> Usuń</a>
    <? endif ?>
  </ul>
  <h1>
    <a href="<?= url_for("invoices") ?>">Faktury</a> \
    <?= $invoice->nr ?>
  </h1>
</div>
  
<dl class="well properties">
  <dt>Numer:
  <dd><?= $invoice->nr ?>
  <dt>Data:
  <dd><?= date('Y-m-d', $invoice->date) ?>
  <dt><a href="<?= url_for("contacts/view.php?id={$invoice->seller}") ?>">Sprzedawca</a>:
  <dd><?= nl2br($invoice->sellerInfo) ?>
  <dt><a href="<?= url_for("contacts/view.php?id={$invoice->buyer}") ?>">Nabywca</a>:
  <dd><?= nl2br($invoice->buyerInfo) ?>
  <dt>Zadania
  <dd>
    <table class="table table-bordered table-condensed">
      <thead>
        <tr>
          <th>Nr
          <th>Pacjent
          <th>Typ pracy
          <th>Ilość
          <th>Cena
          <th class="actions">Akcje
        </tr>
      </thead>
      <tbody class="empty">
        <? if (empty($invoice->tasks)): ?>
        <tr>
          <td colspan="6">
            Faktura nie ma jeszcze przypisanych żadnych zadań.
            <? if (!$invoice->closed): ?>
            <a href="<?= url_for("invoices/edit.php?id={$invoice->id}") ?>">Edytuj fakturę, aby przypisać do niej zadania.</a>
            <? endif ?>
        </tr>
        <? else: ?>
        <? foreach ($invoice->tasks as $task): ?>
        <tr class="task" data-id="<?= $task->id ?>">
          <td><?= e($task->nr) ?>
          <td><a href="<?= url_for("contacts/view.php?id={$task->patient}") ?>"><?= e($task->patientName) ?></a>
          <td><?= e($task->worktypeName) ?>
          <td><?= $task->quantity ?> <?= e($task->unit) ?>
          <td><?= $task->price ?> zł
          <td class="actions">
            <a class="btn" title="Wyświetl szczegóły zadania" href="<?= url_for("tasks/view.php?id={$task->id}") ?>"><i class="icon-list-alt"></i></a>
            <? if (!$invoice->closed): ?>
            <a class="btn btn-danger" title="Usuń przypisanie zadania do faktury" href="<?= url_for("invoices/deassign.php?task={$task->id}&invoice={$invoice->id}") ?>"><i class="icon-minus icon-white"></i></a>
            <? endif ?>
        </tr>
        <? endforeach ?>
        <? endif ?>
      </tbody>
    </table>
</dl>
