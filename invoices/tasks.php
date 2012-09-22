<?php

include_once __DIR__ . '/__common__.php';
include_once __DIR__ . '/../tasks/__common__.php';

bad_request_if(empty($_GET['invoice']));

$invoice = fetch_one('SELECT id, nr, buyer FROM invoices WHERE id=:id LIMIT 1', array(':id' => $_GET['invoice']));

not_found_if(empty($invoice));

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$perPage = empty($_GET['perPage']) ? 15 : (int)$_GET['perPage'];

$pagedTasks = new PagedData($page, $perPage);

$bindings = array(
  ':buyer' => $invoice->buyer
);

$q = <<<SQL
SELECT COUNT(*) AS total
FROM tasks t
INNER JOIN worktypes w ON w.id=t.worktype
WHERE t.closed=0 AND t.doctor=:buyer
SQL;

$totalItems = fetch_one($q, $bindings)->total;

$q = <<<SQL
SELECT
  t.*,
  p.name AS patientName,
  w.name AS worktypeName
FROM tasks t
LEFT JOIN contacts p ON p.id=t.patient
INNER JOIN worktypes w ON w.id=t.worktype
WHERE t.closed=0 AND t.doctor=:buyer
ORDER BY t.id DESC
LIMIT {$pagedTasks->getOffset()}, {$perPage}
SQL;

$tasks = fetch_all($q, $bindings);

$pagedTasks->fill($totalItems, $tasks);

?>

<? decorate('Przypisywanie zadań do faktury') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("invoices") ?>">Faktury</a> \
    <a href="<?= url_for("invoices/view.php?id={$invoice->id}") ?>"><?= $invoice->nr ?></a> \
    Przypisywanie zadań
  </h1>
</div>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Nr
      <th>Data rozpoczęcia
      <th>Czas oddania
      <th>Pacjent
      <th>Typ pracy
      <th class="actions">Akcje
    </tr>
  </thead>
  <tbody id="tasks">
    <? foreach ($pagedTasks AS $task): ?>
    <tr>
      <td><?= e($task->nr) ?>
      <td><?= date('Y-m-d', $task->startDate) ?>
      <td><?= tasks_format_return_time($task->closeDate) ?>
      <td>
        <? if (empty($task->patient)): ?>
        -
        <? else: ?>
        <a href="<?= url_for("contacts/view.php?id={$task->patient}") ?>"><?= e($task->patientName) ?></a>
        <? endif ?>
      <td><?= e($task->worktypeName) ?>
      <td class="actions">
        <a class="btn" title="Wyświetl szczegóły zadania" href="<?= url_for("tasks/view.php?id={$task->id}") ?>"><i class="icon-list-alt"></i></a>
        <a class="btn assign" title="Przypisz zadanie" href="<?= url_for("invoices/assign.php?task={$task->id}&invoice={$invoice->id}") ?>"><i class="icon-plus"></i></a>
        <a class="btn btn-danger deassign" title="Usuń przypisanie zadania" href="<?= url_for("invoices/deassign.php?task={$task->id}&invoice={$invoice->id}") ?>"><i class="icon-minus icon-white"></i></a>
    </tr>
    <? endforeach ?>
  </tbody>
</table>

<?= $pagedTasks->render(url_for("invoice/tasks.php?perPage={$perPage}&invoice={$invoice->id}")) ?>

<? begin_slot('js') ?>
<script>
$(function()
{
  $('#tasks .deassign').hide();

  $('#tasks').on('click', '.assign', function()
  {
    var $assign = $(this);

    if ($assign.hasClass('disabled'))
    {
      return false;
    }

    $assign.addClass('disabled');

    $.ajax({
      method: 'POST',
      url: this.href,
      success: function()
      {
        $assign.hide().next('.deassign').show();
      },
      complete: function()
      {
        $assign.removeClass('disabled');
      }
    });

    return false;
  }).on('click', '.deassign', function()
  {
    var $deassign = $(this);

    if ($deassign.hasClass('disabled'))
    {
      return false;
    }

    $deassign.addClass('disabled');

    $.ajax({
      method: 'POST',
      url: this.href,
      success: function()
      {
        $deassign.hide().prev('.assign').show();
      },
      complete: function()
      {
        $deassign.removeClass('disabled');
      }
    });

    return false;
  });
});
</script>
<? append_slot() ?>
