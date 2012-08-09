<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

$color = fetch_one('SELECT * FROM colors WHERE id=?', array(1 => $_GET['id']));

not_found_if(empty($color));

escape($color);

?>

<? decorate('Kolor') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for("/colors/edit.php?id={$color->id}") ?>"><i class="icon-pencil"></i> Edytuj</a>
    <li><a class="btn btn-danger" href="<?= url_for("/colors/delete.php?id={$color->id}") ?>"><i class="icon-remove icon-white"></i> Usuń</a>
  </ul>
  <h1>
    <a href="<?= url_for("/colors") ?>">Kolory</a> \
    <?= $color->name ?>
  </h1>
</div>
  
<dl class="well properties">
  <dt>Nazwa:
  <dd><?= $color->name ?>
</dl>
