<?php

include __DIR__ . '/../__common__.php';

if (!empty($_POST['quantity']))
{
  $data = $_POST['quantity'];

  exec_insert('quantities', $data);

  $lastId = get_conn()->lastInsertId();

  set_flash('Nowa ilość została dodana pomyślnie!');
  go_to('/quantities/add.php');
}

$quantity = (object)array(
  'name' => ''
);

?>

<? decorate('Dodawanie ilości') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("/quantities") ?>">Ilości</a> \
    Dodawanie
  </h1>
</div>

<form action="<?= url_for("/quantities/add.php") ?>" method=post>
  <? include __DIR__ . '/__form__.php' ?>
</form>
