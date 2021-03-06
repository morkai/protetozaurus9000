<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

$contact = fetch_one('SELECT * FROM contacts WHERE id=?', array(1 => $_GET['id']));

not_found_if(empty($contact));

escape($contact);

?>

<? decorate('Kontakt') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for("contacts/edit.php?id={$contact->id}") ?>"><i class="icon-pencil"></i> Edytuj</a>
    <li><a class="btn btn-danger" href="<?= url_for("contacts/delete.php?id={$contact->id}") ?>"><i class="icon-remove icon-white"></i> Usuń</a>
  </ul>
  <h1>
    <a href="<?= url_for("contacts") ?>">Kontakty</a> \
    <?= $contact->name ?>
  </h1>
</div>

<div class="row-fluid">
  <div class="span9 well">
    <dl class="properties span9">
      <dt>Imię i nazwisko:
      <dd><?= $contact->name ?>
      <dt>Adres:
      <dd><?= nl2br($contact->address) ?>
      <dt>Firma:
      <dd><?= dash_if_empty($contact->company) ?>
      <dt>Stanowisko:
      <dd><?= dash_if_empty($contact->position) ?>
      <dt>Adres e-mail:
      <dd>
        <? if (empty($contact->email)): ?>
        -
        <? else: ?>
        <a href="mailto:<?= $contact->email ?>"><?= $contact->email ?></a>
        <? endif ?>
      <dt>Strona WWW:
      <dd>
        <? if (empty($contact->website)): ?>
        -
        <? else: ?>
        <a href="<?= $contact->website ?>"><?= $contact->website ?></a>
        <? endif ?>
      <dt>Telefon domowy:
      <dd><?= dash_if_empty($contact->telHome) ?>
      <dt>Telefon służbowy:
      <dd><?= dash_if_empty($contact->telWork) ?>
      <dt>Telefon komórkowy:
      <dd><?= dash_if_empty($contact->telMobile) ?>
      <dt>Uwagi:
      <dd><?= empty($contact->comments) ? '-' : nl2br($contact->comments) ?>
    </dl>
  </div>
  <div class="span3 well nav-well">
    <ul class="nav nav-list">
      <li class="nav-header">Listy zadań
      <li><a href="<?= url_for("tasks/?d={$contact->id}") ?>">Zadania jako lekarz</a>
      <li class="nav-header">Inne
      <li><a href="<?= url_for("invoices/?buyer={$contact->id}") ?>">Faktury</a>
      <li><a href="<?= url_for("worktypes/?doctor={$contact->id}") ?>">Cennik prac</a>
    </ul>
  </div>
</div>
