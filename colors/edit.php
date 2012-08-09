<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

$color = fetch_one('SELECT * FROM colors WHERE id=?', array(1 => $_GET['id']));

not_found_if(empty($color));

if (!empty($_POST['color']))
{
  $data = $_POST['color'];

  exec_update('colors', $data, "id={$color->id}");

  set_flash('Kolor został zmodyfikowany pomyślnie!');
  go_to(get_referer("/colors/view.php?id={$color->id}"));
}

escape($color);

?>

<? decorate('Edycja koloru') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("/colors") ?>">Kolory</a> \
    <a href="<?= url_for("/colors/view.php?id={$color->id}") ?>"><?= $color->name ?></a> \
    Edycja
  </h1>
</div>

<form action="<?= url_for("/colors/edit.php?id={$color->id}") ?>" method=post>
  <? include __DIR__ . '/__form__.php' ?>
</form>
