<?php

include __DIR__ . '/../__common__.php';

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$perPage = empty($_GET['perPage']) ? 15 : (int)$_GET['perPage'];

$pagedQuantities = new PagedData($page, $perPage);

$totalItems = fetch_one('SELECT COUNT(*) AS total FROM quantities')->total;

$quantities = fetch_all(sprintf(
  'SELECT * FROM quantities ORDER BY name LIMIT %d,%d',
  $pagedQuantities->getOffset(),
  $perPage
));

$pagedQuantities->fill($totalItems, $quantities);

?>

<? decorate('Ilości') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for('/quantities/add.php') ?>"><i class="icon-plus"></i> Dodaj nową ilość</a>
  </ul>
  <h1>Ilości</h1>
</div>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Nazwa
      <th class="actions">Akcje
    </tr>
  </thead>
  <tbody>
    <? foreach ($pagedQuantities AS $quantity): ?>
    <tr>
      <td><?= e($quantity->name) ?>
      <td class="actions">
        <a class="btn" href="<?= url_for("quantities/view.php?id={$quantity->id}") ?>"><i class="icon-list-alt"></i></a>
        <a class="btn" href="<?= url_for("quantities/edit.php?id={$quantity->id}") ?>"><i class="icon-pencil"></i></a>
        <a class="btn btn-danger" href="<?= url_for("quantities/delete.php?id={$quantity->id}") ?>"><i class="icon-remove icon-white"></i></a>
    </tr>
    <? endforeach ?>
  </tbody>
</table>

<?= $pagedQuantities->render(url_for("quantities/?perPage={$perPage}")) ?>
