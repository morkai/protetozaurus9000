<?php

include __DIR__ . '/../__common__.php';

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$perPage = empty($_GET['perPage']) ? 15 : (int)$_GET['perPage'];

$pagedColors = new PagedData($page, $perPage);

$totalItems = fetch_one('SELECT COUNT(*) AS total FROM colors')->total;

$colors = fetch_all(sprintf(
  'SELECT * FROM colors ORDER BY name LIMIT %d,%d',
  $pagedColors->getOffset(),
  $perPage
));

$pagedColors->fill($totalItems, $colors);

?>

<? decorate('Kolory') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for('/colors/add.php') ?>"><i class="icon-plus"></i> Dodaj nowy kolor</a>
  </ul>
  <h1>Kolory</h1>
</div>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Nazwa
      <th class="actions">Akcje
    </tr>
  </thead>
  <tbody>
    <? foreach ($pagedColors AS $color): ?>
    <tr>
      <td><?= e($color->name) ?>
      <td class="actions">
        <a class="btn" href="<?= url_for("colors/view.php?id={$color->id}") ?>"><i class="icon-list-alt"></i></a>
        <a class="btn" href="<?= url_for("colors/edit.php?id={$color->id}") ?>"><i class="icon-pencil"></i></a>
        <a class="btn btn-danger" href="<?= url_for("colors/delete.php?id={$color->id}") ?>"><i class="icon-remove icon-white"></i></a>
    </tr>
    <? endforeach ?>
  </tbody>
</table>

<?= $pagedColors->render(url_for("colors/?perPage={$perPage}")) ?>
