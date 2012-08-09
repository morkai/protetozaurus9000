<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

$worktype = fetch_one('SELECT * FROM worktypes WHERE id=?', array(1 => $_GET['id']));

not_found_if(empty($worktype));

if (!empty($_POST['worktype']))
{
  $data = $_POST['worktype'];

  exec_update('worktypes', $data, "id={$worktype->id}");

  set_flash('Typ prac został zmodyfikowany pomyślnie!');
  go_to(get_referer("/worktypes/view.php?id={$worktype->id}"));
}

?>

<? decorate('Edycja koloru') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("/worktypes") ?>">Typy prac</a> \
    <a href="<?= url_for("/worktypes/view.php?id={$worktype->id}") ?>"><?= $worktype->name ?></a> \
    Edycja
  </h1>
</div>

<form action="<?= url_for("/worktypes/edit.php?id={$worktype->id}") ?>" method=post>
  <? include __DIR__ . '/__form__.php' ?>
</form>
