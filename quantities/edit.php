<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

$quantity = fetch_one('SELECT * FROM quantities WHERE id=?', array(1 => $_GET['id']));

not_found_if(empty($quantity));

if (!empty($_POST['quantity']))
{
  $data = $_POST['quantity'];

  exec_update('quantities', $data, "id={$quantity->id}");

  set_flash('Ilość została zmodyfikowana pomyślnie!');
  go_to(get_referer("/quantities/view.php?id={$quantity->id}"));
}

escape($quantity);

?>

<? decorate('Edycja ilości') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("/quantities") ?>">Ilości</a> \
    <a href="<?= url_for("/quantities/view.php?id={$quantity->id}") ?>"><?= $quantity->name ?></a> \
    Edycja
  </h1>
</div>

<form action="<?= url_for("/quantities/edit.php?id={$quantity->id}") ?>" method=post>
  <? include __DIR__ . '/__form__.php' ?>
</form>
