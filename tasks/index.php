<?php

include __DIR__ . '/../__common__.php';

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$perPage = empty($_GET['perPage']) ? 15 : (int)$_GET['perPage'];

$pagedTasks = new PagedData($page, $perPage);

$filterDateParam = function($param)
{
  return !empty($_GET[$param]) && is_string($_GET[$param]) && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_GET[$param]) ? $_GET[$param] : '';
};

$where = '';
$conditions = array();
$filter = array(
  'w' => !empty($_GET['w']) && is_array($_GET['w']) ? array_filter($_GET['w'], 'is_numeric') : array(),
  'd' => !empty($_GET['d']) && is_string($_GET['d']) ? escape(trim($_GET['d'])) : '',
  'p' => !empty($_GET['p']) && is_string($_GET['p']) ? escape(trim($_GET['p'])) : '',
  'sd' => $filterDateParam('sd'),
  'cd' => $filterDateParam('cd'),
  'tsd' => $filterDateParam('tsd'),
  'tcd' => $filterDateParam('tcd')
);

if (!empty($filter['sd']))
{
  if (empty($filter['tsd']))
  {
    $conditions[] = 't.startDate=' . strtotime($filter['sd']);
  }
  else
  {
    $conditions[] = 't.startDate BETWEEN ' . strtotime($filter['sd']) . ' AND ' . strtotime($filter['tsd']);
  }
}

if (!empty($filter['cd']))
{
  if (empty($filter['tcd']))
  {
    $conditions[] = 't.closeDate=' . strtotime($filter['cd']);
  }
  else
  {
    $conditions[] = 't.closeDate BETWEEN ' . strtotime($filter['cd']) . ' AND ' . strtotime($filter['tcd']);
  }
}

if (!empty($filter['w']))
{
  $conditions[] = 't.worktype IN(' . implode(',', $filter['w']) . ')';
}

if (strlen($filter['d']) >= 3)
{
  $conditions[] = "d.name LIKE '%" . addslashes($filter['d']) . "%'";
}

if (strlen($filter['p']) >= 3)
{
  $conditions[] = "p.name LIKE '%" . addslashes($filter['p']) . "%'";
}

if (!empty($conditions))
{
  $where = 'WHERE ' . implode(' AND ', $conditions);
}

$q = <<<SQL
SELECT COUNT(*) AS total
FROM tasks t
LEFT JOIN contacts d ON d.id=t.doctor
LEFT JOIN contacts p ON p.id=t.patient
INNER JOIN worktypes w ON w.id=t.worktype
{$where}
SQL;

$totalItems = fetch_one($q)->total;

$q = <<<SQL
SELECT
  t.*,
  d.name AS doctorName,
  p.name AS patientName,
  w.name AS worktypeName
FROM tasks t
LEFT JOIN contacts d ON d.id=t.doctor
LEFT JOIN contacts p ON p.id=t.patient
INNER JOIN worktypes w ON w.id=t.worktype
{$where}
ORDER BY t.id DESC
LIMIT {$pagedTasks->getOffset()}, {$perPage}
SQL;

$tasks = fetch_all($q);

$pagedTasks->fill($totalItems, $tasks);

$worktypes = fetch_array('SELECT id AS `key`, name AS `value` FROM worktypes ORDER BY name ASC');
$quantities = fetch_array('SELECT id AS `key`, name AS `value` FROM quantities ORDER BY name ASC');
$colors = fetch_array('SELECT id AS `key`, name AS `value` FROM colors ORDER BY name ASC');

?>

<? decorate('Zadania') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><button id=filterTasks class="btn" data-toggle="button"><i class="icon-filter"></i> Filtruj</button>
    <li><a class="btn" href="<?= url_for('/tasks/add.php') ?>"><i class="icon-plus"></i> Dodaj nowe zadanie</a>
  </ul>
  <h1>Zadania</h1>
</div>

<form id="tasksFilter" class="well form-inline" action="<?= url_for("/tasks/") ?>">
  <input type="hidden" name="perPage" value="<?= $perPage ?>">
  <div class="row">
    <div class="control-group span2">
      <label for=filter-startDate class="control-label">Data rozpoczęcia:</label>
      <input id=filter-startDate class="span2" name=sd type=date value="<?= e($filter['sd']) ?>">
      <br>
      <label for=filter-toStartDate class="control-label">Data rozpoczęcia (do):</label>
      <input id=filter-toStartDate class="span2" name=tsd type=date value="<?= e($filter['tsd']) ?>">
    </div>
    <div class="control-group span2">
      <label for=filter-closeDate class="control-label">Data oddania:</label>
      <input id=filter-closeDate class="span2" name=cd type=date value="<?= e($filter['cd']) ?>">
      <br>
      <label for=filter-toCloseDate class="control-label">Data oddania (do):</label>
      <input id=filter-toCloseDate class="span2" name=tcd type=date value="<?= e($filter['tcd']) ?>">
    </div>
    <div class="control-group span3">
      <label for=filter-doctor class="control-label">Lekarz:</label>
      <input id=filter-doctor class="span3" name=d type=text value="<?= e($filter['d']) ?>">
      <br>
      <label for=filter-patient class="control-label">Pacjent:</label>
      <input id=filter-patient class="span3" name=p type=text value="<?= e($filter['p']) ?>">
    </div>
    <div class="control-group span4">
      <label for=filter-worktypes class="control-label">Typy prac:</label>
      <select id=filter-worktypes class="span4" name=w[] multiple data-placeholder="Wybierz typy prac...">
        <?= render_options($worktypes, $filter['w']) ?>
      </select>
    </div>
  </div>
  <input class="btn btn-primary" type="submit" value="Filtruj zadania">
  <a class="btn" href="<?= url_for("/tasks") ?>">Wyczyść filtry</a>
</form>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Nr
      <th>Typ pracy
      <th>Data rozpoczęcia
      <th>Data oddania
      <th>Lekarz
      <th>Pacjent
      <th class="actions">Akcje
    </tr>
  </thead>
  <tbody>
    <? foreach ($pagedTasks AS $task): ?>
    <tr>
      <td><?= e($task->nr) ?>
      <td><?= e($task->worktypeName) ?>
      <td><?= date('Y-m-d', $task->startDate) ?>
      <td><?= empty($task->closeDate) ? '-' : date('Y-m-d', $task->closeDate) ?>
      <td>
        <? if (empty($task->doctor)): ?>
        -
        <? else: ?>
        <a href="<?= url_for("contacts/view.php?id={$task->doctor}") ?>"><?= e($task->doctorName) ?></a>
        <? endif ?>
      <td>
        <? if (empty($task->patient)): ?>
        -
        <? else: ?>
        <a href="<?= url_for("contacts/view.php?id={$task->patient}") ?>"><?= e($task->patientName) ?></a>
        <? endif ?>
      <td class="actions">
        <a class="btn" href="<?= url_for("tasks/view.php?id={$task->id}") ?>"><i class="icon-list-alt"></i></a>
        <a class="btn" href="<?= url_for("tasks/edit.php?id={$task->id}") ?>"><i class="icon-pencil"></i></a>
        <a class="btn btn-danger" href="<?= url_for("tasks/delete.php?id={$task->id}") ?>"><i class="icon-remove icon-white"></i></a>
    </tr>
    <? endforeach ?>
  </tbody>
</table>

<?= $pagedTasks->render(url_for("tasks/?perPage={$perPage}&amp;" . http_build_query($filter))) ?>

<? begin_slot('js') ?>
<script>
$(function()
{
  var filter = <?= empty($conditions) ? 'false' : 'true' ?>;

  var $worktypeFilter = $('#filter-worktypes').chosen();

  $(window).resize(function()
  {
    var newWidth = $worktypeFilter.show().outerWidth(true);

    $worktypeFilter.hide().next('.chzn-container').width(newWidth).find('.chzn-drop').width(newWidth - 2);
  }).resize();

  var $filterTasks = $('#filterTasks');
  var $tasksFilter = $('#tasksFilter');

  $filterTasks.click(function()
  {
    $tasksFilter.toggle();
  });

  if (filter)
  {
    $filterTasks.addClass('active');
  }
  else
  {
    $tasksFilter.hide();
  }
});
</script>
<? append_slot() ?>
