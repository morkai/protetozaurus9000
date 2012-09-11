<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

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

$q = <<<SQL
SELECT w.id, w.name, COALESCE(p.price, w.price) AS price
FROM worktypes w
LEFT JOIN worktype_prices p ON w.id=p.worktype
AND p.doctor=:doctor
WHERE w.id=:worktype
LIMIT 1
SQL;

$worktype = fetch_one($q, array(
  ':worktype' => $_GET['id'],
  ':doctor' => $doctorId
));

not_found_if(empty($worktype));

?>

<? decorate('Typ prac') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for("worktypes/edit.php?id={$worktype->id}&doctor={$doctorId}") ?>"><i class="icon-pencil"></i> Edytuj</a>
    <li><a class="btn btn-danger" href="<?= url_for("worktypes/delete.php?id={$worktype->id}") ?>"><i class="icon-remove icon-white"></i> Usuń</a>
  </ul>
  <h1>
    <a href="<?= url_for("worktypes/?doctor={$doctorId}") ?>">Typy prac</a>
    <? if (!empty($doctor)): ?>
    dla <a href="<?= url_for("contacts/view.php?id={$doctorId}") ?>"><?= e($doctor->name) ?></a>
    <? endif ?>
    \
    <?= e($worktype->name) ?>
  </h1>
</div>

<dl class="well properties">
  <dt>Nazwa:
  <dd><?= e($worktype->name) ?>
  <dt><?= empty($doctor) ? 'Domyślna cena:' : 'Cena:' ?>
  <dd><?= $worktype->price ?> zł
</dl>
