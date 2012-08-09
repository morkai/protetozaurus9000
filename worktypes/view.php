<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

$worktype = fetch_one('SELECT * FROM worktypes WHERE id=?', array(1 => $_GET['id']));

not_found_if(empty($worktype));

?>

<? decorate('Typ prac') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for("/worktypes/edit.php?id={$worktype->id}") ?>"><i class="icon-pencil"></i> Edytuj</a>
    <li><a class="btn btn-danger" href="<?= url_for("/worktypes/delete.php?id={$worktype->id}") ?>"><i class="icon-remove icon-white"></i> Usuń</a>
  </ul>
  <h1>
    <a href="<?= url_for("/worktypes") ?>">Typy prac</a> \
    <?= $worktype->name ?>
  </h1>
</div>
  
<dl class="well properties">
  <dt>Nazwa:
  <dd><?= $worktype->name ?>
</dl>
