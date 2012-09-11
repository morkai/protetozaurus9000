<?php

include __DIR__ . '/../__common__.php';

if (!empty($_POST['color']))
{
  $data = $_POST['color'];

  exec_insert('colors', $data);

  $lastId = get_conn()->lastInsertId();

  set_flash('Nowy kolor został dodany pomyślnie!');
  go_to('/colors/add.php');
}

$color = (object)array(
  'name' => ''
);

?>

<? decorate('Dodawanie koloru') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("colors") ?>">Kolory</a> \
    Dodawanie
  </h1>
</div>

<form action="<?= url_for("colors/add.php") ?>" method=post>
  <? include __DIR__ . '/__form__.php' ?>
</form>
