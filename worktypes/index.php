<?php

include __DIR__ . '/../__common__.php';

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$perPage = empty($_GET['perPage']) ? 15 : (int)$_GET['perPage'];

if (!empty($_GET['doctor']))
{
  $doctor = fetch_one('SELECT id, name FROM contacts WHERE id=:id LIMIT 1', array(':id' => $_GET['doctor']));

  not_found_if(empty($doctor));

  $doctorId = $doctor->id;
}
else
{
  $doctorId = 0;
}

$pagedWorktypes = new PagedData($page, $perPage);

$totalItems = fetch_one('SELECT COUNT(*) AS total FROM worktypes')->total;

$q = <<<SQL
SELECT w.id, w.name, COALESCE(p.price, w.price) AS price
FROM worktypes w
LEFT JOIN worktype_prices p ON w.id=p.worktype
AND p.doctor=:doctor
ORDER BY w.name ASC
LIMIT {$perPage}
OFFSET {$pagedWorktypes->getOffset()}
SQL;

$worktypes = fetch_all($q, array(':doctor' => $doctorId));

$pagedWorktypes->fill($totalItems, $worktypes);

$disabledClass = empty($doctor) ? '' : 'disabled';

?>

<? decorate('Typy prac') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn <?= $disabledClass ?>" href="<?= url_for("worktypes/add.php") ?>"><i class="icon-plus"></i> Dodaj nowy typ prac</a>
  </ul>
  <h1>
    Typy prac
    <? if (!empty($doctor)): ?>
    dla <a href="<?= url_for("contacts/view.php?id={$doctor->id}") ?>"><?= e($doctor->name) ?></a>
    <? endif ?>
  </h1>
</div>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Nazwa
      <th><?= empty($doctor) ? 'Domyślna cena' : 'Cena' ?>
      <th class="actions">Akcje
    </tr>
  </thead>
  <tbody>
    <? foreach ($pagedWorktypes AS $worktype): ?>
    <tr>
      <td><?= e($worktype->name) ?>
      <td><?= $worktype->price ?> zł
      <td class="actions">
        <a class="btn" title="Wyświetl szczegóły typu pracy" href="<?= url_for("worktypes/view.php?id={$worktype->id}&doctor={$doctorId}") ?>"><i class="icon-list-alt"></i></a>
        <a class="btn" title="Edytuj typ pracy" href="<?= url_for("worktypes/edit.php?id={$worktype->id}&doctor={$doctorId}") ?>"><i class="icon-pencil"></i></a>
        <a class="btn btn-danger <?= $disabledClass ?>" title="Usuń typ pracy" href="<?= url_for("worktypes/delete.php?id={$worktype->id}") ?>"><i class="icon-remove icon-white"></i></a>
    </tr>
    <? endforeach ?>
  </tbody>
</table>

<?= $pagedWorktypes->render(url_for("worktypes/?perPage={$perPage}&doctor={$doctorId}")) ?>
