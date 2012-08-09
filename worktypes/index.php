<?php

include __DIR__ . '/../__common__.php';

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$perPage = empty($_GET['perPage']) ? 15 : (int)$_GET['perPage'];

$pagedWorktypes = new PagedData($page, $perPage);

$totalItems = fetch_one('SELECT COUNT(*) AS total FROM worktypes')->total;

$worktypes = fetch_all(sprintf(
  'SELECT * FROM worktypes ORDER BY name LIMIT %d,%d',
  $pagedWorktypes->getOffset(),
  $perPage
));

$pagedWorktypes->fill($totalItems, $worktypes);

?>

<? decorate('Typy prac') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for('/worktypes/add.php') ?>"><i class="icon-plus"></i> Dodaj nowy typ prac</a>
  </ul>
  <h1>Typy prac</h1>
</div>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Nazwa
      <th class="actions">Akcje
    </tr>
  </thead>
  <tbody>
    <? foreach ($pagedWorktypes AS $worktype): ?>
    <tr>
      <td><?= $worktype->name ?>
      <td class="actions">
        <a class="btn" href="<?= url_for("worktypes/view.php?id={$worktype->id}") ?>"><i class="icon-list-alt"></i></a>
        <a class="btn" href="<?= url_for("worktypes/edit.php?id={$worktype->id}") ?>"><i class="icon-pencil"></i></a>
        <a class="btn btn-danger" href="<?= url_for("worktypes/delete.php?id={$worktype->id}") ?>"><i class="icon-remove icon-white"></i></a>
    </tr>
    <? endforeach ?>
  </tbody>
</table>

<?= $pagedWorktypes->render(url_for("worktypes/?perPage={$perPage}")) ?>
