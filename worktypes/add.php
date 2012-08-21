<?php

include __DIR__ . '/../__common__.php';

if (!empty($_POST['worktype']))
{
  $data = $_POST['worktype'];

  exec_insert('worktypes', $data);

  $lastId = get_conn()->lastInsertId();

  set_flash('Nowy typ prac został dodany pomyślnie!');
  go_to('/worktypes/add.php');
}

$worktype = (object)array(
  'name' => '',
  'price' => '0.00'
);

?>

<? decorate('Dodawanie typu pracy') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("/worktypes") ?>">Typy prac</a> \
    Dodawanie
  </h1>
</div>

<form action="<?= url_for("/worktypes/add.php") ?>" method=post>
  <? include __DIR__ . '/__form__.php' ?>
</form>
