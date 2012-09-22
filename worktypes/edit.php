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

if (!empty($_POST['worktype']))
{
  $data = $_POST['worktype'];

  if (empty($doctor))
  {
    exec_update('worktypes', $data, "id={$worktype->id}");
  }
  else
  {
    $db = get_conn();

    $db->beginTransaction();

    try
    {
      exec_update('worktypes', array('name' => $data['name']), "id={$worktype->id}");
      exec_stmt('REPLACE worktype_prices SET doctor=:doctor, worktype=:worktype, price=:price', array(
        ':doctor' => $doctor->id,
        ':worktype' => $worktype->id,
        ':price' => $data['price']
      ));

      $db->commit();
    }
    catch (PDOException $x)
    {
      set_flash($x->getMessage(), 'error');

      $db->rollBack();

      goto VIEW;
    }
  }

  set_flash('Typ prac został zmodyfikowany pomyślnie!');
  go_to(get_referer("/worktypes/view.php?id={$worktype->id}&doctor={$doctorId}"));
}

VIEW:

?>

<? decorate('Edycja typu prac') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("worktypes/?doctor={$doctorId}") ?>">Typy prac</a>
    <? if (!empty($doctor)): ?>
    dla <a href="<?= url_for("contacts/view.php?id={$doctorId}") ?>"><?= e($doctor->name) ?></a>
    <? endif ?>
    \
    <a href="<?= url_for("worktypes/view.php?id={$worktype->id}&doctor={$doctorId}") ?>"><?= $worktype->name ?></a> \
    Edycja
  </h1>
</div>

<form action="<?= url_for("worktypes/edit.php?id={$worktype->id}&doctor={$doctorId}") ?>" method=post>
  <? include __DIR__ . '/__form__.php' ?>
</form>
