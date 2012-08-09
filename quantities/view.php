<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

$quantity = fetch_one('SELECT * FROM quantities WHERE id=?', array(1 => $_GET['id']));

not_found_if(empty($quantity));

escape($quantity);

?>

<? decorate('Kolor') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for("/quantities/edit.php?id={$quantity->id}") ?>"><i class="icon-pencil"></i> Edytuj</a>
    <li><a class="btn btn-danger" href="<?= url_for("/quantities/delete.php?id={$quantity->id}") ?>"><i class="icon-remove icon-white"></i> Usuń</a>
  </ul>
  <h1>
    <a href="<?= url_for("/quantities") ?>">Ilości</a> \
    <?= $quantity->name ?>
  </h1>
</div>
  
<dl class="well properties">
  <dt>Nazwa:
  <dd><?= $quantity->name ?>
</dl>
